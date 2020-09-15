<header-component
:profile= "{{ json_encode(\GlobalHelper::instance()->bladeHelper()) }}"
:hallway= "{{ json_encode(request()->route()->getName() === 'eventHallway') }} "
:locale= "{{ json_encode(\GlobalHelper::instance()->getLocale()) }}"
></header-component>
