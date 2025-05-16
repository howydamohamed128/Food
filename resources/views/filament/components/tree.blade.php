@php
    $tree = \App\Http\Resources\CategoriesTreeResource::collection(\App\Models\Catalog\Category::whereNull("parent_id")->get());
@endphp
<div class="bg-white p-4 rounded"
     wire:ignore
     x-load-js="@js([
     \Filament\Support\Facades\FilamentAsset::getScriptSrc('jquery'),
     \Filament\Support\Facades\FilamentAsset::getScriptSrc('jstree')
     ])"
     x-load-css="@js([
    \Filament\Support\Facades\FilamentAsset::getStyleHref('jstree'),
    \Filament\Support\Facades\FilamentAsset::getStyleHref('jstree-ext'),
    \Filament\Support\Facades\FilamentAsset::getStyleHref('fontawesome'),
    ])"
>
    <div id="categories_tree"></div>

</div>


<script>
    window.addEventListener("DOMContentLoaded", function () {
        let categoryTreeSelector = $("#categories_tree");

        initTree(categoryTreeSelector);

        function sendCategoriesArrangementList(list) {
            $.ajax({
                url: "{{route("cp.categories.arrange")}}",
                data: {list},
                success: function (data) {
                    new FilamentNotification()
                        .title('Saved successfully')
                        .icon('heroicon-o-document-text')
                        .iconColor('success')
                        .send()
                }
            })
        }

        function initTree(selector) {
            if (selector.length) {
                selector.jstree({
                    core: {
                        check_callback: true,
                        data: @json($tree)
                    },
                    plugins: ['types', 'dnd'],
                    types: {
                        default: {
                            icon: "fas fa-folder-open"
                        },
                    }
                }).on('move_node.jstree', function (e, data) {
                    let list = [], id, parent;
                    let treeList = categoryTreeSelector.jstree(true).get_json('#', {flat: true})
                    treeList.forEach(function (el) {
                        parent = (el.parent === "#") ? null : el.parent;
                        id = el.id;
                        list.push({"id": id, "parent": parent});
                    });
                    sendCategoriesArrangementList(list);

                });
            }
        }
    });
</script>

