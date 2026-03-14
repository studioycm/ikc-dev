<livewire:legacy.breeding.club-membership-compact
    :membership-state="$membershipState ?? null"
    :key="'club-membership-compact-' . md5(json_encode($membershipState ?? []))"
/>
