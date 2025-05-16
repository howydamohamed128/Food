<?php

namespace App\Filament\Pages\Auth;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use bootstrap\Filament\Pages\Auth\view;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Facades\Filament;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns;
use Filament\Pages\Page;
use Filament\Panel;
use Filament\Support\Enums\Alignment;
use Filament\Support\Exceptions\Halt;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rules\Password;

/**
 * @property Form $form
 */
class EditProfile extends Page {
    use Concerns\HasRoutes;
    use Concerns\InteractsWithFormActions;
    use HasPageShield;

    protected static bool $shouldRegisterNavigation = false;
    /**
     * @var view-string
     */
    protected static string $view = 'filament.pages.auth.edit-profile';


    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public static function getLabel(): string {
        return __('menu.edit_profile');
    }

    public static function routes(Panel $panel): void {
        $slug = static::getSlug();

        Route::get("/{$slug}", static::class)
            ->middleware(static::getRouteMiddleware($panel))
            ->withoutMiddleware(static::getWithoutRouteMiddleware($panel))
            ->name('profile');
    }

    /**
     * @return string | array<string>
     */
    public static function getRouteMiddleware(Panel $panel): string|array {
        return [
            ...(static::isEmailVerificationRequired($panel) ? [static::getEmailVerifiedMiddleware($panel)] : []),
            ...Arr::wrap(static::$routeMiddleware),
        ];
    }

    public function mount(): void {
        $this->fillForm();
    }

    public function getUser(): Authenticatable & Model {
        $user = Filament::auth()->user();

        if (!$user instanceof Model) {
            throw new Exception('The authenticated user object must be an Eloquent model to allow the profile page to update it.');
        }

        return $user;
    }

    protected function fillForm(): void {
        $data = $this->getUser()->attributesToArray();

        $this->callHook('beforeFill');

        $data = $this->mutateFormDataBeforeFill($data);

        $this->form->fill($data);

        $this->callHook('afterFill');
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array {
        return $data;
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array {
        return $data;
    }

    public function save(): void {
        try {
            $this->callHook('beforeValidate');

            $data = $this->form->getState();

            $this->callHook('afterValidate');

            $data = $this->mutateFormDataBeforeSave($data);

            $this->callHook('beforeSave');

            $this->handleRecordUpdate($this->getUser(), $data);

            $this->callHook('afterSave');
        } catch (Halt $exception) {
            return;
        }

        if (request()->hasSession() && array_key_exists('password', $data)) {
            request()->session()->put([
                'password_hash_' . Filament::getAuthGuard() => $data['password'],
            ]);
        }

        $this->data['password'] = null;
        $this->data['passwordConfirmation'] = null;

        $this->getSavedNotification()?->send();

        if ($redirectUrl = $this->getRedirectUrl()) {
            $this->redirect($redirectUrl);
        }
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function handleRecordUpdate(Model $record, array $data): Model {
        $record->update($data);

        return $record;
    }

    protected function getSavedNotification(): ?Notification {
        $title = $this->getSavedNotificationTitle();

        if (blank($title)) {
            return null;
        }

        return Notification::make()
            ->success()
            ->title($this->getSavedNotificationTitle());
    }

    protected function getSavedNotificationTitle(): ?string {
        return __('filament-panels::pages/auth/edit-profile.notifications.saved.title');
    }

    protected function getRedirectUrl(): ?string {
        return null;
    }

    protected function getAvatarFormComponent(): Component {
        return SpatieMediaLibraryFileUpload::make('avatar')
            ->columnSpan(['xl' => 2])
            ->nullable();
    }

    protected function getNameFormComponent(): Component {
        return TextInput::make('name')
            ->label(__('forms.fields.name'))
            ->required()
            ->columnSpan(2)
            ->maxLength(255)
            ->autofocus();
    }

   

    protected function getEmailFormComponent(): Component {
        return TextInput::make('email')
            ->label(__('filament-panels::pages/auth/edit-profile.form.email.label'))
            ->email()
            ->required()
            ->columnSpan(2)
            ->maxLength(255)
            ->unique(ignoreRecord: true);
    }
    protected function getPhoneFormComponent(): Component {
        return TextInput::make('phone')
            ->label(__('forms.fields.phone'))
            ->required()
            ->columnSpan(2)
            ->maxLength(255)
            ->unique(ignoreRecord: true);
    }
   

    protected function getPasswordFormComponent(): Component {
        return TextInput::make('password')
            ->label(__('filament-panels::pages/auth/edit-profile.form.password.label'))
            ->password()
            ->rule(Password::default())
            ->autocomplete('new-password')
            ->revealable()
            ->dehydrated(fn($state): bool => filled($state))
            ->live(debounce: 500)
            ->columnSpan(2)
            ->same('passwordConfirmation');
    }

    protected function getPasswordConfirmationFormComponent(): Component {
        return TextInput::make('passwordConfirmation')
            ->label(__('filament-panels::pages/auth/edit-profile.form.password_confirmation.label'))
            ->password()
            ->revealable()
            ->required()
            ->visible(fn(Get $get): bool => filled($get('password')))
            ->dehydrated(false);
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
                        $this->getAvatarFormComponent(),
                        $this->getNameFormComponent(),
                        $this->getPhoneFormComponent(),
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                    ])
                    ->operation('edit')
                    ->model($this->getUser())
                    ->statePath('data')
                ,
            )->columns(2),
        ];
    }

    /**
     * @return array<Action | ActionGroup>
     */
    protected function getFormActions(): array {
        return [
            $this->getSaveFormAction(),
            $this->getCancelFormAction(),
        ];
    }

    protected function getCancelFormAction(): Action {
        return $this->backAction();
    }

    protected function getSaveFormAction(): Action {
        return Action::make('save')
            ->label(__('filament-panels::pages/auth/edit-profile.form.actions.save.label'))
            ->submit('save')
            ->keyBindings(['mod+s']);
    }

    protected function hasFullWidthFormActions(): bool {
        return false;
    }

    public function getFormActionsAlignment(): string|Alignment {
        return Alignment::Start;
    }

    public function getTitle(): string|Htmlable {
        return static::getLabel();
    }

    public static function getSlug(): string {
        return static::$slug ?? 'profile';
    }

    public function hasLogo(): bool {
        return false;
    }

    /**
     * @deprecated Use `getCancelFormAction()` instead.
     */
    public function backAction(): Action {
        return Action::make('back')
            ->label(__('filament-panels::pages/auth/edit-profile.actions.cancel.label'))
            ->url(filament()->getUrl())
            ->color('gray');
    }

}