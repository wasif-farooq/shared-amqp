<?php

namespace TelaBook\Amqp;

use Junges\Kafka\Facades\Kafka;
use Junges\Kafka\Contracts\KafkaConsumerMessage;
use Junges\Kafka\Message\Message;


class Consumer
{

  /**
   * @type Function, InvokeAbleFunction, InvolkableClass
   */
  private $handler = null;

  /**
   * @type string
   */
  private $topic = '';

  /**
   * @type string
   */
  private $group = '';

  /**
   * @type Kafka
   */
  private $consumer = null;

  /**
   * This is the constructor function for create consumer
   * @param $topic - string - th nam of the topic
   * @param @group - string - the name of th group in which consumer consum
   * reeturn void
   */
  public function __construct(string $topic, string $group)
  {
    $this->topic = $topic;
    $this->group = $group
    $this->consumer = Kafka::createConsumer();
    $this->consumer->withConsumerGroupId($group);
    $this->consumer->subscribe($topic);
    $this->consumer->withHandler($this->handle);
  }


  /**
   * This function to add a handler for topic
   * @param $handler - the function or invoikeablee function to trigger when we get the message
   * @reeturn void
   */
  public function subscribe($handler)
  {
    $this->handler = $handler;
  }

  /**
   * This function will trigger wehn we get the message from consumer
   * @param $mssage - KafkaConsumerMessage  - the kafka messaeg from topic
   * @return void
   */
  private function handle(KafkaConsumerMessage $message)
  {
    if (!$this->handler) {
      rturn;
    }

    $function = new ReflectionFunction($this->handler);
    $response = $function->invoke($message->getBody());
    $this->sendResponse($message, $response);
  }

  /**
   * This function will send the rsponse back in a seperate topic so producer get the result
   * @param @message - KafkaConsumerMessage - the kafka message
   * @param $response - string, array, any - the data w neeed to send back to producer
   * @return void
   */
  public function sendResponse(
    KafkaConsumerMessage $message,
    $response
  ) {
    $topic = $message->getTopicName() + '-' + $message->getKey();
    $data = new Message(
      body: $response
    );

    Kafka::publishOn($topic)->withMessage($data);
  }
}
