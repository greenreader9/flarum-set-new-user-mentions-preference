<?php

namespace PT\Preferences\Listeners;

use Flarum\Core\Notification\NotificationSyncer;
use Flarum\Core\User;
use Flarum\Event\UserWillBeSaved;
use Illuminate\Contracts\Events\Dispatcher;

class SetEmailPrefNewUser
{
    const USER_PREFERENCES = [
        'email' => [
            'userMentioned' => true
        ],
    ];

    const MAIN_PREFERENCES = [
        'followAfterReply' => false,
    ];

    /**
     * @var NotificationSyncer
     */
    protected $notifications;

    /**
     * @param NotificationSyncer $notifications
     */
    public function __construct(NotificationSyncer $notifications)
    {
        $this->notifications = $notifications;
    }

    /**
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen(UserWillBeSaved::class, [$this, 'beforeUserWillBeSaved']);
    }

    /**
     * @param UserWillBeSaved $event
     */
    public function beforeUserWillBeSaved(UserWillBeSaved $event)
    {
        /** @var User $user */
        $user = $event->user;

        if($user->exists) {
            return;
        }

        foreach(self::getDefaultUserPreferences() as $key => $value) {

            $user->setPreference($key, $value);
        }
    }

    /**
     * @return array
     */
    public static function getDefaultUserPreferences()
    {
        $preferences = [];

        foreach (self::USER_PREFERENCES as $method => $types) {
            foreach ($types as $type => $value) {
                $preferences [User::getNotificationPreferenceKey($type, $method)] = $value;
            }
        }

        foreach (self::MAIN_PREFERENCES as $type => $value) {
            $preferences [$type] = $value;
        }

        return $preferences;
    }
}
