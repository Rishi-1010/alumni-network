CREATE TABLE IF NOT EXISTS `events` (
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `event_date` date NOT NULL,
  `event_time` time NOT NULL,
  `location` varchar(255) NOT NULL,
  `event_type` enum('physical','virtual') NOT NULL DEFAULT 'physical',
  `max_attendees` int(11) NOT NULL,
  `registration_deadline` datetime NOT NULL,
  `status` enum('upcoming','ongoing','past','cancelled') NOT NULL DEFAULT 'upcoming',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; 