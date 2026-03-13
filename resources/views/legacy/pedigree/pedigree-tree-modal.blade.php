@php
    $resolvedSettings = array_replace_recursive(
        config('pedigree_tree.presets.user_widget_modal', []),
        $settings ?? [],
    );
@endphp

<div>
    <livewire:legacy.pedigree.pedigree-tree
        :dog-id="$dogId"
        :show-builder="$showBuilder ?? false"
        :settings="$resolvedSettings"
        :key="'pedigree-tree-modal-'.$dogId"
    />
</div>
