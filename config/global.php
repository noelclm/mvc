<?php

/**
 * Copyright 2017 Noel Clemente
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

// BBDD
define("SERVER_CONNECTION", "localhost");
define("USER_CONNECTION", "mvc");
define("PSW_CONNECTION", "password");
define("BBDD_CONNECTION", "mvc");
define("ENCODING", "utf8");
define("PERSISTENT", false);

// Parameters
define("IDLE_TIME", 604800); // Seconds (1 Day = 86400, 1 Week = 604800, Infinite = -1)
define("DEFAULT_ROUTE", "/signin");
define("MULTIPLE_SESSIONS", true);