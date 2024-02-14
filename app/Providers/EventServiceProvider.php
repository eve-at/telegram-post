<?php

namespace App\Providers;

use App\Events\AdPublished;
use App\Events\ChannelDeleted;
use App\Events\ChannelSessionChanged;
use App\Events\ChannelUpdated;
use App\Events\MessagePublished;
use App\Events\MessageUnpublished;
use App\Listeners\AdPublishedListener;
use App\Listeners\ChannelDeletedListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Listeners\ChannelSessionChangedListener;
use App\Listeners\ChannelSessionInitListener;
use App\Listeners\ChannelSessionListListener;
use App\Listeners\ChannelUpdatedListener;
use App\Listeners\MessagePublishedListener;
use App\Listeners\MessageUnpublishedListener;
use App\Models\Channel;
use App\Models\Message;
use App\Observers\ChannelObserver;
use App\Observers\MessageObserver;
use Illuminate\Auth\Events\Login;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,            
        ],
        Login::class => [
            ChannelSessionInitListener::class,
            ChannelSessionListListener::class,
        ],
        ChannelSessionChanged::class => [
            ChannelSessionChangedListener::class
        ],
        ChannelUpdated::class => [
            ChannelUpdatedListener::class,
            ChannelSessionListListener::class,
        ],
        ChannelDeleted::class => [
            ChannelDeletedListener::class,
            ChannelSessionListListener::class
        ],
        MessagePublished::class => [
            MessagePublishedListener::class
        ],
        AdPublished::class => [
            AdPublishedListener::class
        ],
        MessageUnpublished::class => [
            MessageUnpublishedListener::class
        ],
    ];

    protected $observers = [
        Channel::class => [ChannelObserver::class],
        Message::class => [MessageObserver::class],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
