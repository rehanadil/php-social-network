<?php

/**
 * This code was generated by
 * \ / _    _  _|   _  _
 * | (_)\/(_)(_|\/| |(/_  v1.0.0
 * /       /
 */

namespace Twilio\Rest\Notify\V1\Service;

use Twilio\ListResource;
use Twilio\Options;
use Twilio\Values;
use Twilio\Version;

class NotificationList extends ListResource {
    /**
     * Construct the NotificationList
     * 
     * @param Version $version Version that contains the resource
     * @param string $serviceSid The service_sid
     * @return \Twilio\Rest\Notify\V1\Service\NotificationList 
     */
    public function __construct(Version $version, $serviceSid) {
        parent::__construct($version);

        // Path Solution
        $this->solution = array(
            'serviceSid' => $serviceSid,
        );

        $this->uri = '/Services/' . rawurlencode($serviceSid) . '/Notifications';
    }

    /**
     * Create a new NotificationInstance
     * 
     * @param array|Options $options Optional Arguments
     * @return NotificationInstance Newly created NotificationInstance
     */
    public function create($options = array()) {
        $options = new Values($options);

        $data = Values::of(array(
            'Identity' => $options['identity'],
            'Tag' => $options['tag'],
            'Body' => $options['body'],
            'Priority' => $options['priority'],
            'Ttl' => $options['ttl'],
            'Title' => $options['title'],
            'Sound' => $options['sound'],
            'Action' => $options['action'],
            'Data' => $options['data'],
            'Apn' => $options['apn'],
            'Gcm' => $options['gcm'],
            'Sms' => $options['sms'],
            'FacebookMessenger' => $options['facebookMessenger'],
            'Fcm' => $options['fcm'],
            'Segment' => $options['segment'],
        ));

        $payload = $this->version->create(
            'POST',
            $this->uri,
            array(),
            $data
        );

        return new NotificationInstance(
            $this->version,
            $payload,
            $this->solution['serviceSid']
        );
    }

    /**
     * Provide a friendly representation
     * 
     * @return string Machine friendly representation
     */
    public function __toString() {
        return '[Twilio.Notify.V1.NotificationList]';
    }
}