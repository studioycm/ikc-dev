<div class="space-y-4">
    <div>
        {{ $this->form }}
    </div>

    @if($canRenderChildren)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                @if($subject->father)
                    <livewire:legacy.pedigree.parents-pair-form
                        :subjectId="$subject->father->getKey()"
                        :depth="$depth + 1"
                        :maxDepth="$maxDepth"
                        :expanded="false"
                        :key="'father-'.$subject->father->getKey().'-'.$depth"/>
                @endif
            </div>
            <div>
                @if($subject->mother)
                    <livewire:legacy.pedigree.parents-pair-form
                        :subjectId="$subject->mother->getKey()"
                        :depth="$depth + 1"
                        :maxDepth="$maxDepth"
                        :expanded="false"
                        :key="'mother-'.$subject->mother->getKey().'-'.$depth"/>
                @endif
            </div>
        </div>
    @endif

    <x-filament-actions::modals/>
</div>
