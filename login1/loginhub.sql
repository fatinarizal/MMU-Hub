SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Database: `loginhub`

-- Table structure for table `admins`
CREATE TABLE `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL UNIQUE,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL UNIQUE,
  `phone` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` ENUM('admin') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `admins`
INSERT INTO `admins` (`username`, `name`, `email`, `phone`, `password`, `role`) VALUES
('fatin', 'Fatin Aina', 'fatin@admin.mmu.edu.my','0123456789', '$1234567', 'admin'),
('aminah', 'Aminah', 'aminah@admin.mmu.edu.my','017548954', '$1234567890', 'admin');

-- Table structure for table `users`
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL UNIQUE,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL UNIQUE,
  `phone` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` ENUM('student', 'staff') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `users`
INSERT INTO `users` (`username`, `name`, `email`, `phone`, `password`, `role`) VALUES
('fatimah', 'Fatimah', 'fatimah@student.mmu.edu.my','010022112', '$4527avs', 'student'),
('azizah', 'Azizah Maisarah', 'azizah@student.mmu.edu.my','011223343', '$123b', 'staff');

-- Table structure for table `faqs`
CREATE TABLE `faqs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `question` TEXT NOT NULL,
    `answer` TEXT NOT NULL,
    `status` ENUM('pending', 'answered') DEFAULT 'pending',
    `submitted_by` INT,
    `answered_by` INT DEFAULT NULL,
    `submitted_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `answered_at` TIMESTAMP NULL,
    FOREIGN KEY (`submitted_by`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`answered_by`) REFERENCES `admins`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `faqs`
INSERT INTO `faqs` (`question`, `answer`, `status`, `submitted_by`, `answered_by`) VALUES
('How can I book a facility on MMU Hub?', 'To book a facility, log in to your MMU Hub account, navigate to the Venue Bookings section, select your desired venue and time slot, add to cart and confirm your booking.', 'answered', 1, 1),
('Can I cancel my booking after confirmation?', 'Yes, you can cancel your booking from the “Booking Slots” section in your account dashboard.', 'answered',  2, 2),
('Is there any fee for booking a sports venue?', '', 'pending', 1, NULL);

-- Table structure for table messages
CREATE TABLE messages (
    messageID INT(11) NOT NULL AUTO_INCREMENT,  -- Primary Key
    content VARCHAR(500) NOT NULL,              -- Message content (500 character limit)
    createdTime TIMESTAMP DEFAULT CURRENT_TIMESTAMP,  -- Time message was created
    attachment VARCHAR(50),                   -- Attachment file name
    id INT(11) NOT NULL,                    -- Foreign Key to reference the sender from users table
    recipientID INT(11) NOT NULL,               -- Foreign Key to reference the recipient from users table
    PRIMARY KEY (messageID),                    -- Define messageID as the primary key
    FOREIGN KEY (id) REFERENCES users(id) ON DELETE CASCADE,  -- Foreign Key referencing id column in users table
    FOREIGN KEY (recipientID) REFERENCES users(id) ON DELETE CASCADE  -- Foreign Key referencing id column in users table
);

-- Dumping data for table messages
INSERT INTO messages (content, createdTime, attachment,id,recipientID) VALUES
('Hello, how are you?', '2024-09-22 10:15:30', NULL, 1, 2),  -- User 1 sends to User 2
('Please check the assignment details.', '2024-09-22 10:17:45', NULL, 2, 1);  -- User 2 sends to User 1

-- Table structure for table `venue`
CREATE TABLE `venue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `venue`
INSERT INTO `venue` (`name`, `location`) VALUES 
('Main Lecture Hall', 'Level G, Faculty of Computing and Informatics'),
('Badminton Court', 'Sports Complex'),
('Football Field', 'MMU Stadium'),
('Basketball Court', 'Sports Complex'),
('Learning Point', 'Library Building');

-- Table structure for table `cart`
CREATE TABLE `cart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `startTime` time NOT NULL,
  `endTime` time NOT NULL,  
  `users_id` INT NOT NULL,
  `venue_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`users_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`venue_id`) REFERENCES `venue`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data into `cart` table
INSERT INTO `cart` (`date`, `startTime`, `endTime`, `users_id`, `venue_id`) VALUES 
('2024-09-23', '10:00:00', '12:00:00', 1, 2),
('2024-09-24', '14:00:00', '16:00:00', 1, 3),
('2024-09-25', '09:00:00', '11:00:00', 2, 1),
('2024-09-26', '15:00:00', '17:00:00', 2, 3),
('2024-09-23', '10:00:00', '12:00:00', 2, 1),
('2024-09-27', '15:00:00', '17:00:00', 2, 2),
('2024-09-28', '08:00:00', '10:00:00', 1, 5),  
('2024-09-28', '11:00:00', '13:00:00', 2, 4),  
('2024-09-29', '14:00:00', '16:00:00', 1, 3),  
('2024-09-30', '09:00:00', '11:00:00', 2, 5),  
('2024-10-01', '10:00:00', '12:00:00', 1, 4);  

-- Table structure for table `booking`
CREATE TABLE `booking` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` boolean NOT NULL DEFAULT 0, -- 0 = Cancel Booking, 1 = Booked
  `dateCreated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `bookingDate` date NOT NULL,
  `bookingStartTime` time NOT NULL,
  `bookingEndTime` time NOT NULL,
  `users_id` int(11) NOT NULL,
  `venue_id` int(11) NOT NULL,
  `cart_id` int(11),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`users_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`venue_id`) REFERENCES `venue`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`cart_id`) REFERENCES `cart`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data into `booking` table
INSERT INTO `booking` (`status`, `bookingDate`, `bookingStartTime`, `bookingEndTime`, `users_id`, `venue_id`, `cart_id`) VALUES
(1, '2024-09-23', '10:00:00', '12:00:00', 1, 1, NULL), -- Booking for user 1 (Fatimah) for venue 1 (Main Lecture Hall) from cart 1
(1, '2024-09-25', '09:00:00', '11:00:00', 2, 3, NULL), -- Booking for user 2 (Azizah) for venue 3 (Learning Point) from cart 3
(0, '2024-09-24', '14:00:00', '16:00:00', 1, 2, NULL), -- Not booked entry for user 1 (Fatimah) for venue 2 (Badminton Court) from cart 2
(1, '2024-09-28', '08:00:00', '10:00:00', 1, 4, NULL),  -- User 1 (Fatimah) booked Basketball Court
(1, '2024-09-28', '11:00:00', '13:00:00', 2, 5, NULL),  -- User 2 (Azizah) booked Learning Point
(0, '2024-09-29', '14:00:00', '16:00:00', 2, 3, NULL),  -- User 1 (Fatimah) did not book Football Field
(1, '2024-09-30', '09:00:00', '11:00:00', 2, 2, NULL),  -- User 2 (Azizah) booked Badminton Court
(1, '2024-10-01', '10:00:00', '12:00:00', 1, 1, NULL);  -- User 1 (Fatimah) booked Main Lecture Hall

-- Table structure for table `feedback`
CREATE TABLE `feedback` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `feedback` TEXT NOT NULL,
  `createdTime` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `users_id` INT,
  FOREIGN KEY (`users_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data into `feedback` table
INSERT INTO `feedback` (`feedback`, `createdTime`, `users_id`) VALUES
  ('This is a great service! I really appreciate the help.', CURRENT_TIMESTAMP, 1),
  ('I encountered an issue with my booking, but the support was quick to respond.', CURRENT_TIMESTAMP, 2);

-- Table structure for table `announcements`
 CREATE TABLE `announcements` (
    `announcementID` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(50) NOT NULL,
    `content` VARCHAR(500) NOT NULL,
    `image` VARCHAR(50),
    `file` VARCHAR(50),
    `createdTime` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Dumping data into `announcements` table
INSERT INTO `announcements` (`title`, `content`, `image`, `file`) VALUES
('Welcome to the Platform!', 'We are excited to welcome all MMU students and staff to our new platform.', 'welcome.jpg', NULL),
('Important Update: System Maintenance', 'Please be informed that there will be a scheduled maintenance on 25th September 2024.', NULL, NULL);


-- AUTO_INCREMENT for table `users`
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

-- AUTO_INCREMENT for table `faqs`
ALTER TABLE `faqs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

-- AUTO_INCREMENT for table `admins`
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

-- AUTO_INCREMENT for table `messages`
ALTER TABLE `messages`
  MODIFY `messageID` INT(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

-- AUTO_INCREMENT for table `venue`
ALTER TABLE `venue`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

-- AUTO_INCREMENT for table `cart`
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

-- AUTO_INCREMENT for table `booking`
ALTER TABLE `booking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

-- AUTO_INCREMENT for table `feedback`
ALTER TABLE `feedback`
  MODIFY `id` INT NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;


COMMIT;
