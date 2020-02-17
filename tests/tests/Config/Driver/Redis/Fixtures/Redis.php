<?php

if (!class_exists(Redis::class)) {
    class Redis
    {

        /**
         * This method is declared so that Mockery can properly detect the `&$iterator` pass by reference when the redis
         * extension is not loaded.
         */
        public function scan(&$iterator, $pattern = null, $count = 0)
        {
        }

    }
}
