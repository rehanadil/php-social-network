<?php

/**
 * This code was generated by
 * \ / _    _  _|   _  _
 * | (_)\/(_)(_|\/| |(/_  v1.0.0
 * /       /
 */

namespace Twilio\Rest\Messaging\V1\Service;

use Twilio\InstanceContext;
use Twilio\Values;
use Twilio\Version;

class AlphaSenderContext extends InstanceContext {
    /**
     * Initialize the AlphaSenderContext
     * 
     * @param \Twilio\Version $version Version that contains the resource
     * @param string $serviceSid The service_sid
     * @param string $sid The sid
     * @return \Twilio\Rest\Messaging\V1\Service\AlphaSenderContext 
     */
    public function __construct(Version $version, $serviceSid, $sid) {
        parent::__construct($version);

        // Path Solution
        $this->solution = array(
            'serviceSid' => $serviceSid,
            'sid' => $sid,
        );

        $this->uri = '/Services/' . rawurlencode($serviceSid) . '/AlphaSenders/' . rawurlencode($sid) . '';
    }

    /**
     * Fetch a AlphaSenderInstance
     * 
     * @return AlphaSenderInstance Fetched AlphaSenderInstance
     */
    public function fetch() {
        $params = Values::of(array());

        $payload = $this->version->fetch(
            'GET',
            $this->uri,
            $params
        );

        return new AlphaSenderInstance(
            $this->version,
            $payload,
            $this->solution['serviceSid'],
            $this->solution['sid']
        );
    }

    /**
     * Deletes the AlphaSenderInstance
     * 
     * @return boolean True if delete succeeds, false otherwise
     */
    public function delete() {
        return $this->version->delete('delete', $this->uri);
    }

    /**
     * Provide a friendly representation
     * 
     * @return string Machine friendly representation
     */
    public function __toString() {
        $context = array();
        foreach ($this->solution as $key => $value) {
            $context[] = "$key=$value";
        }
        return '[Twilio.Messaging.V1.AlphaSenderContext ' . implode(' ', $context) . ']';
    }
}