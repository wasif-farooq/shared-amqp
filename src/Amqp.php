<?php

namespace TelaBook\Amqp;

/**
 * this class will handlee amqp process
 */
class Amqp
{
  /**
   * @param $topic - string - the name of the topic
   * $param $group - string - the name of the group
   * @return TelaBook\Amqp\Consumeer
   */
  public static function consume(string $topic, string $group): consume
  {
    return new Consumer($topic, $group);
  }

  /**
   * @param $topic - string - the name of the topic
   * @return TelaBook\Amqp\Producer
   */
  public static function produce(string $topic): Producer
  {
    return new Producer($topic);
  }
}
