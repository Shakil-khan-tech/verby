<?php
// Header menu
return [

    'en' => [
        'items' => [
            [],
            // [
            //     'title' => 'Dashboard',
            //     'root' => true,
            //     'icon' => 'media/svg/icons/Design/Layers.svg',
            //     'page' => '/en',
            //     // 'new-tab' => false,
            // ],
            [
                'title' => 'Users',
                'root' => true,
                'icon' => 'media/svg/icons/Communication/Shield-user.svg',
                'page' => '/en/users',
                // 'bullet' => 'dot',
                'submenu' => [
                    'type' => 'classic',
                    'alignment' => 'left',
                    'items' => [
                        [
                            'title' => 'Users',
                            'page' => '/en/users',
                            'desc' => '',
                            'icon' => 'media/svg/icons/Communication/Shield-user.svg',
                        ],
                        [
                            'title' => 'Roles',
                            'page' => '/en/roles',
                            'desc' => '',
                            'icon' => 'media/svg/icons/Home/Key.svg',
                        ],
                        [
                            'title' => 'Emails',
                            'root' => true,
                            'icon' => 'media/svg/icons/Communication/Mail.svg',
                            'page' => '/en/emails',
                        ],
                    ]
                ]
            ],
            [
                'title' => 'Devices',
                'root' => true,
                'icon' => 'media/svg/icons/Devices/Tablet.svg',
                'page' => '/en/devices',
                'submenu' => [
                    'type' => 'classic',
                    'alignment' => 'left',
                    'items' => [
                        [
                            'title' => 'Devices',
                            'root' => true,
                            'icon' => 'media/svg/icons/Devices/Tablet.svg',
                            'page' => '/en/devices',
                        ],
                        [
                            'title' => 'Plan',
                            'root' => true,
                            'icon' => 'media/svg/icons/Layout/Layout-grid.svg',
                            'page' => '/en/plans',
                        ],
                        [
                            'title' => 'Calendar',
                            'root' => true,
                            'icon' => 'media/svg/icons/General/gen014.svg',
                            'page' => '/en/calendars',
                        ],
                        [
                            'title' => 'Issues',
                            'root' => true,
                            'icon' => 'media/svg/icons/General/Clipboard.svg',
                            'page' => '/en/issues/listings',
                        ],
                        [
                            'title' => 'Supplies',
                            'root' => true,
                            'icon' => 'media/svg/icons/Communication/Clipboard-check.svg',
                            'page' => '/en/supplies/listings',
                        ],
                    ]
                ]
            ],
            [
                'title' => 'Records',
                'root' => true,
                'icon' => 'media/svg/icons/Files/Cloud-download.svg',
                'page' => '/en/devices',
                'submenu' => [
                    'type' => 'classic',
                    'alignment' => 'left',
                    'items' => [
                        [
                            'title' => 'Records - List View',
                            'root' => true,
                            'icon' => 'media/svg/icons/Files/Cloud-download.svg',
                            'page' => '/en/records',
                        ],
                        [
                            'title' => 'Records - Calendar View',
                            'root' => true,
                            'icon' => 'media/svg/icons/Files/Cloud-download.svg',
                            'page' => '/en/records/calendar',
                        ],
                    ]
                ]
            ],
            [
                'title' => 'Reports',
                'root' => true,
                'icon' => 'media/svg/icons/Communication/Clipboard-check.svg',
                'page' => '/en/devices',
                'submenu' => [
                    'type' => 'classic',
                    'alignment' => 'left',
                    'items' => [
                        [
                            'title' => 'Individual Monthly Performance',
                            'root' => true,
                            'icon' => 'media/svg/icons/Communication/Clipboard-check.svg',
                            'page' => '/en/records/calendar_report',
                        ],
                        [
                            'title' => 'Monthly Reports',
                            'root' => true,
                            'icon' => 'media/svg/icons/Home/ClockMonth.svg',
                            'page' => '/en/monthly_reports',
                        ],
                        [
                            'title' => 'Daily Reports',
                            'root' => true,
                            'icon' => 'media/svg/icons/Home/Clock.svg',
                            'page' => '/en/daily_reports',
                        ],
                        [
                            'title' => 'Daily Reports - Hotel',
                            'root' => true,
                            'icon' => 'media/svg/icons/Home/ClockHotel.svg',
                            'page' => '/en/daily_reports_hotel',
                        ],
                        [
                            'title' => 'Monthly Performance',
                            'root' => true,
                            'icon' => 'media/svg/icons/Home/ClockMonth.svg',
                            'page' => '/en/monthly_performance',
                        ],
                        [
                            'title' => 'Budget',
                            'root' => true,
                            'icon' => 'media/svg/icons/Home/ClockMonth.svg',
                            'page' => '/en/budget',
                        ],
                        [
                            'title' => 'Statistics',
                            'icon' => 'media/svg/icons/Media/Equalizer.svg',
                            'root' => true,
                            'page' => '/en/employees/statistics',
                        ],
                    ]
                ]
            ],
            [
                'title' => 'Employees',
                'root' => true,
                'icon' => 'media/svg/icons/Communication/Group.svg',
                'page' => '/en/employees',
                'submenu' => [
                    'type' => 'classic',
                    'alignment' => 'left',
                    'items' => [
                        [
                            'title' => 'Employees',
                            'page' => '/en/employees',
                            'desc' => '',
                            'icon' => 'media/svg/icons/Communication/Group.svg',
                        ],
                        [
                            'title' => 'Deleted Employees',
                            'page' => '/en/employees/deleted',
                            'desc' => '',
                            'icon' => 'media/svg/icons/Communication/Group-deleted.svg',
                        ],
                        [
                            'title' => 'Reminder',
                            'root' => true,
                            'icon' => 'media/svg/icons/Home/Clock.svg',
                            'page' => '/en/reminders',
                        ],
                        [
                            'title' => 'Payroll',
                            'root' => true,
                            'icon' => 'media/svg/icons/General/Clipboard.svg',
                            'page' => '/en/lohn',
                        ],
                        [
                            'title' => 'Vacations',
                            'icon' => 'media/svg/icons/Food/Coffee1.svg',
                            'root' => true,
                            'page' => '/en/vacations',
                        ],
                        [
                            'title' => 'Contract',
                            'icon' => 'media/svg/icons/General/Clipboard.svg',
                            'root' => true,
                            'page' => '/en/contracts',
                        ],
                    ]
                ]
            ],
            [
                'title' => 'Holidays',
                'root' => true,
                'icon' => 'media/svg/icons/Home/Clock.svg',
                'page' => '/en/holidays',
            ],
        ]
    ],
    'de' => [
        'items' => [
            [],
            // [
            //     'title' => 'Dashboard',
            //     'root' => true,
            //     'icon' => 'media/svg/icons/Design/Layers.svg',
            //     'page' => '/de',
            //     // 'new-tab' => false,
            // ],
            [
                'title' => 'Benutzer',
                'root' => true,
                'icon' => 'media/svg/icons/Communication/Shield-user.svg',
                'page' => '/de/users',
                // 'bullet' => 'dot',
                'submenu' => [
                    'type' => 'classic',
                    'alignment' => 'left',
                    'items' => [
                        [
                            'title' => 'Benutzer',
                            'page' => '/de/users',
                            'desc' => '',
                            'icon' => 'media/svg/icons/Communication/Shield-user.svg',
                        ],
                        [
                            'title' => 'Rollen',
                            'page' => '/de/roles',
                            'desc' => '',
                            'icon' => 'media/svg/icons/Home/Key.svg',
                        ],
                        [
                            'title' => 'Emails',
                            'root' => true,
                            'icon' => 'media/svg/icons/Communication/Mail.svg',
                            'page' => '/de/emails',
                        ],
                    ]
                ]
            ],
            [
                'title' => 'Geräte',
                'root' => true,
                'icon' => 'media/svg/icons/Devices/Tablet.svg',
                'page' => '/de/devices',
                'submenu' => [
                    'type' => 'classic',
                    'alignment' => 'left',
                    'items' => [
                        [
                            'title' => 'Geräte',
                            'root' => true,
                            'icon' => 'media/svg/icons/Devices/Tablet.svg',
                            'page' => '/de/devices',
                        ],
                        [
                            'title' => 'Planen',
                            'root' => true,
                            'icon' => 'media/svg/icons/Layout/Layout-grid.svg',
                            'page' => '/de/plans',
                        ],
                        [
                            'title' => 'Kalender',
                            'root' => true,
                            'icon' => 'media/svg/icons/Communication/Clipboard-check.svg',
                            'page' => '/de/calendars',
                        ],
                        [
                            'title' => 'Probleme',
                            'root' => true,
                            'icon' => 'media/svg/icons/General/Clipboard.svg',
                            'page' => '/de/issues/listings',
                        ],
                        [
                            'title' => 'Versorgung',
                            'root' => true,
                            'icon' => 'media/svg/icons/Communication/Clipboard-check.svg',
                            'page' => '/de/supplies/listings',
                        ],
                    ]
                ]
            ],
            [
                'title' => 'Aufzeichnungen',
                'root' => true,
                'icon' => 'media/svg/icons/Files/Cloud-download.svg',
                'page' => '/de/devices',
                'submenu' => [
                    'type' => 'classic',
                    'alignment' => 'left',
                    'items' => [
                        [
                            'title' => 'Aufzeichnungen - Liste',
                            'root' => true,
                            'icon' => 'media/svg/icons/Files/Cloud-download.svg',
                            'page' => '/de/records',
                        ],
                        [
                            'title' => 'Aufzeichnungen - Kalender',
                            'root' => true,
                            'icon' => 'media/svg/icons/Files/Cloud-download.svg',
                            'page' => '/de/records/calendar',
                        ],
                    ]
                ]
            ],
            [
                'title' => 'Berichte',
                'root' => true,
                'icon' => 'media/svg/icons/Communication/Clipboard-check.svg',
                'page' => '/en/devices',
                'submenu' => [
                    'type' => 'classic',
                    'alignment' => 'left',
                    'items' => [
                        [
                            'title' => 'Individuelle monatliche Leistung',
                            'root' => true,
                            'icon' => 'media/svg/icons/Communication/Clipboard-check.svg',
                            'page' => '/de/records/calendar_report',
                        ],
                        [
                            'title' => 'Monatsberichte',
                            'root' => true,
                            'icon' => 'media/svg/icons/Home/ClockMonth.svg',
                            'page' => '/de/monthly_reports',
                        ],
                        [
                            'title' => 'Tagesberichte',
                            'root' => true,
                            'icon' => 'media/svg/icons/Home/Clock.svg',
                            'page' => '/de/daily_reports',
                        ],
                        [
                            'title' => 'Tagesberichte - Hotel',
                            'root' => true,
                            'icon' => 'media/svg/icons/Home/ClockHotel.svg',
                            'page' => '/de/daily_reports_hotel',
                        ],
                        [
                            'title' => 'Monthly Performance',
                            'root' => true,
                            'icon' => 'media/svg/icons/Home/ClockMonth.svg',
                            'page' => '/de/monthly_performance',
                        ],
                        [
                            'title' => 'Budget',
                            'root' => true,
                            'icon' => 'media/svg/icons/Home/ClockMonth.svg',
                            'page' => '/de/budget',
                        ],
                        [
                            'title' => 'Statistiken',
                            'icon' => 'media/svg/icons/Media/Equalizer.svg',
                            'root' => true,
                            'page' => '/de/employees/statistics',
                        ],
                    ]
                ]
            ],
            [
                'title' => 'Mitarbeiter',
                'root' => true,
                'icon' => 'media/svg/icons/Communication/Group.svg',
                'page' => '/de/employees',
                'submenu' => [
                    'type' => 'classic',
                    'alignment' => 'left',
                    'items' => [
                        [
                            'title' => 'Mitarbeiter',
                            'page' => '/de/employees',
                            'desc' => '',
                            'icon' => 'media/svg/icons/Communication/Group.svg',
                        ],
                        [
                            'title' => 'Gelöschte Mitarbeiter',
                            'page' => '/de/employees/deleted',
                            'desc' => '',
                            'icon' => 'media/svg/icons/Communication/Group-deleted.svg',
                        ],
                        [
                            'title' => 'Erinnerung',
                            'root' => true,
                            'icon' => 'media/svg/icons/Home/Clock.svg',
                            'page' => '/de/reminders',
                        ],
                        [
                            'title' => 'Lohnabrechnung',
                            'root' => true,
                            'icon' => 'media/svg/icons/General/Clipboard.svg',
                            'page' => '/de/lohn',
                        ],
                        [
                            'title' => 'Urlaube',
                            'icon' => 'media/svg/icons/Food/Coffee1.svg',
                            'root' => true,
                            'page' => '/de/vacations',
                        ],
                        [
                            'title' => 'Vertrag',
                            'icon' => 'media/svg/icons/General/Clipboard.svg',
                            'root' => true,
                            'page' => '/de/contracts',
                        ],
                    ]
                ]
            ],
            [
                'title' => 'Feiertage',
                'root' => true,
                'icon' => 'media/svg/icons/Home/Clock.svg',
                'page' => '/de/holidays',
            ],
        ]
    ],

];
