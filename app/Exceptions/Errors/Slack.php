<?php

namespace app\Exceptions\Errors;

use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;

class Slack extends Notification
{
    use Notifiable;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var string
     */
    protected $slack_webhook_url;

    /**
     * Slack constructor.
	 * The message should have either a message or attachments specified
	 * but these can be added after construction
	 * @param $message : (Optional) Content of the message for Slack
     */
    public function __construct($message = null)
    {
        $this->message($message);
        $this->slack_webhook_url = config('slack.endpoint');
        $this->attachments = [];
    }

    /**
     * Route notifications for the Slack channel.
     *
     * @return string
     */
    public function routeNotificationForSlack()
    {
        return $this->slack_webhook_url;
	}

	/**
	 * Set the content message
	 * This facilitates creation of the object before defining the message content.
	 * @param $message - Content of the message for Slack
	 */
	public function message($message)
	{
		$this->message = $message;
    }

	/**
	 * Add a slack attachment
	 * An attachment is essentially just a list of options
	 * @param $attachment - array of options for attachment
	 */
	public function attach($attachment)
	{
		$this->attachments[] = $attachment;
	}
    /**
     * Get the Slack representation of the notification
	 * Add in capability to use attachments.
	 * Only a subset of attachment options are supported at present.
     *
     * @param  $notifiable
     * @return SlackMessage
     */
    public function toSlack($notifiable)
    {
    	// Create the message with the elements which must always be included.
		$fullMessage = (new SlackMessage)
			->from(config('slack.username'))
			->to( config('slack.channel'));

		// If we have a message add it as content
		if ( !empty($this->message) ) {
			$fullMessage->content($this->message);
		}

		// Add any attachments
		foreach ($this->attachments as $attachment) {
			$fullMessage->attachment(function ($attachTo) use ($attachment) {
				// We need to call the appropriate method on SlackMessage for each attribute
				// Only a subset have been implemented
				if (isset($attachment["title"])) {
					if (isset($attachment["title_link"])) {
						$attachTo->title($attachment["title"], $attachment["title_link"]);
					} else {
						$attachTo->title($attachment["title"]);
					}
				}
				if (isset($attachment["text"])) {
					$attachTo->text($attachment["text"]);
				}
				if (isset($attachment["color"])) {
					$attachTo->color($attachment["color"]);
				}
				if (isset($attachment["fallback"])) {
					$attachTo->fallback($attachment["fallback"]);
				}
			});
		}
        return $fullMessage;
    }
}