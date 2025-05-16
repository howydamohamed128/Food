<?php

namespace App\Filament\Pages;

use App\Models\User;
use App\Models\VerificationCode;
use Closure;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Facades\Filament;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Models\Contracts\FilamentUser;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\SimplePage;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\ValidationException;


/**
 * @property Form $form
 */
class LoginPage extends SimplePage {
    use InteractsWithFormActions;
    use WithRateLimiting;

    /**
     * @var view-string
     */
    protected static string $view = 'filament-panels::pages.auth.login';

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];


    public function mount(): void {
        if (Filament::auth()->check()) {
            redirect()->intended(Filament::getUrl());
        }

        $this->form->fill();
    }

    public function authenticate(): ?LoginResponse {

        $data = $this->form->getState();
        $accept_terms_before = false;
//        if ($data['step'] == 1) {
//
//            $this->sendOtpCodeProcess($data);
//            if (User::where('phone', $data['phone'])->where('active', 1)->first()?->email_verified_at) {
//                $accept_terms_before = true;
//            }
//            $this->form->fill([...$this->form->getState(), 'step' => 2, 'accept_terms_before' => $accept_terms_before]);
//            return null;
//        }

        return $this->loginProcess($data);

    }

    public function checkRateLimit() {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            Notification::make()
                ->title(__('filament-panels::pages/auth/login.notifications.throttled.title', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]))
                ->body(array_key_exists('body', __('filament-panels::pages/auth/login.notifications.throttled') ?: []) ? __('filament-panels::pages/auth/login.notifications.throttled.body', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]) : null)
                ->danger()
                ->send();

            return null;
        }

    }

    public function loginProcess($data) {

        if (!Filament::auth()->attempt($this->getCredentialsFromFormData($data), $data['remember'] ?? false)) {
            $this->throwFailureValidationException();
        }

        if (!Filament::auth()->user()->active) {
            auth()->logout();
            $this->throwFailureValidationException();
        }

        $user = Filament::auth()->user();

        if (
            ($user instanceof FilamentUser) &&
            (!$user->canAccessPanel(Filament::getCurrentPanel()))
        ) {
            Filament::auth()->logout();

            $this->throwFailureValidationException();
        }
        Filament::auth()->user()->update(['email_verified_at' => now()]);
        VerificationCode::where("phone", auth()->user()->phone)->delete();
        session()->regenerate();

        return app(LoginResponse::class);
    }

    public function sendOtpCodeProcess($data) {
        if (!Filament::auth()->once($this->getCredentialsFromFormData($data))) {
            $this->throwFailureValidationException();
        }
        if (!User::where('phone', $data['phone'])->where('active', 1)->exists()) {
            $this->throwFailureValidationException();
        }

        SendVerificationCode::run($data['phone']);

        return null;
    }

    protected function throwFailureValidationException(): never {
        throw ValidationException::withMessages([
            'data.password' => __('filament-panels::pages/auth/login.messages.failed'),
        ]);
    }

    public function form(Form $form): Form {
        return $form;
    }

    /**
     * @return array<int | string, string | Form>
     */
    protected function getForms(): array {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        Group::make([
                            Hidden::make("step")->default(1),
                            $this->getEmailFormComponent(),
                            $this->getPasswordFormComponent(),
                            $this->getRememberFormComponent(),
                        ])->extraAttributes(fn($get) => $get('step') == 2 ? ['class' => 'hidden'] : []),

                        $this->getOTPCodeComponent()->visible(fn($get) => $get('step') == 2),
                        Checkbox::make('accept_terms')->label(function () {
                            $terms = __("forms.fields.terms_and_conditions");
                            return new HtmlString(
                                __("forms.fields.i_accept") . " " .
                                <<<Blade
           <div class='inline-block' x-on:click="\$dispatch('open-modal',{id:'terms_and_conditions_modal'})"> $terms</div>
Blade

                            );
                        })
                            ->required()
                            ->visible(fn($get) => $get('step') == 2 && !$get('accept_terms_before')),
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    protected function getEmailFormComponent(): Component {
        return TextInput::make('phone')
            ->required()
            ->live();

    }

    protected function getPasswordFormComponent(): Component {
        return TextInput::make('password')
            ->label(__('filament-panels::pages/auth/login.form.password.label'))
            ->hint(filament()->hasPasswordReset() ? new HtmlString(Blade::render('<x-filament::link :href="filament()->getRequestPasswordResetUrl()"> {{ __(\'filament-panels::pages/auth/login.actions.request_password_reset.label\') }}</x-filament::link>')) : null)
            ->password()
            ->live()
            ->autocomplete('current-password')
            ->required()
            ->extraInputAttributes(['tabindex' => 2]);
    }

    protected function getRememberFormComponent(): Component {
        return Checkbox::make('remember')
            ->label(__('filament-panels::pages/auth/login.form.remember.label'));
    }

    public function getOTPCodeComponent() {
        return TextInput::make('otp_code')
            ->required()
            ->rules([
                fn($get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {

                    if (!VerificationCode::where('phone', $get('phone'))->where('code', $value)->exists()) {
                        $fail(__('validation.api.invalid_otp'));
                    }
                },
            ]);
    }

    public function registerAction(): Action {
        return Action::make('register')
            ->link()
            ->label(__('filament-panels::pages/auth/login.actions.register.label'))
            ->url(filament()->getRegistrationUrl());
    }

    public function getTitle(): string|Htmlable {
        return __('filament-panels::pages/auth/login.title');
    }

    public function getHeading(): string|Htmlable {
        return __('filament-panels::pages/auth/login.heading');
    }

    /**
     * @return array<Action | ActionGroup>
     */
    protected function getFormActions(): array {
        return [
            $this->getAuthenticateFormAction(),
        ];
    }

    protected function getAuthenticateFormAction(): Action {
        return Action::make('authenticate')
            ->label(__('filament-panels::pages/auth/login.form.actions.authenticate.label'))
            ->submit('authenticate');
    }

    protected function hasFullWidthFormActions(): bool {
        return true;
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    protected function getCredentialsFromFormData(array $data): array {

        return [
            'phone' => $data['phone'],
            'password' => $data['password'],
        ];
    }
}
