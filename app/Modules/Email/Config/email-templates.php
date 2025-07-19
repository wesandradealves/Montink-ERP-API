<?php

return [
    'order_confirmation' => [
        'subject' => 'messages.email.order_confirmation_subject',
        'view' => 'emails.order-confirmation', // Usa view blade se existir
        'sections' => [
            'header' => [
                'greeting' => 'messages.email.order_confirmation_greeting',
                'intro' => 'messages.email.order_confirmation_intro',
            ],
            'body' => [
                'status' => 'messages.email.order_confirmation_status',
                'delivery_address' => 'messages.email.delivery_address_title',
            ],
            'items' => [
                'header' => 'messages.email.order_confirmation_items',
                'template' => '- :name x :quantity - :price',
            ],
            'footer' => [
                'total' => 'messages.email.order_confirmation_total',
                'thank_you' => 'messages.email.order_confirmation_footer',
            ],
        ],
    ],
    
    'order_cancelled' => [
        'subject' => 'messages.email.order_cancelled_subject',
        'sections' => [
            'header' => [
                'greeting' => 'messages.email.order_cancelled_greeting',
                'intro' => 'messages.email.order_cancelled_intro',
            ],
            'footer' => [
                'contact' => 'messages.email.order_cancelled_footer',
            ],
        ],
    ],
    
    'order_status_update' => [
        'subject' => 'messages.email.order_status_update_subject',
        'sections' => [
            'header' => [
                'greeting' => 'messages.email.order_status_update_greeting',
                'intro' => 'messages.email.order_status_update_intro',
            ],
            'body' => [
                'new_status' => 'messages.email.order_status_update_new_status',
            ],
            'footer' => [
                'track' => 'messages.email.order_status_update_track',
            ],
        ],
    ],
    
    'weekly_report' => [
        'subject' => 'messages.email.weekly_report_subject',
        'sections' => [
            'header' => [
                'title' => 'messages.email.weekly_report_title',
                'period' => 'messages.email.weekly_report_period',
            ],
            'body' => [
                'total_orders' => 'messages.email.weekly_report_total_orders',
                'total_revenue' => 'messages.email.weekly_report_total_revenue',
                'average_ticket' => 'messages.email.weekly_report_average_ticket',
            ],
            'footer' => [
                'generated' => 'messages.email.weekly_report_generated',
            ],
        ],
    ],
];