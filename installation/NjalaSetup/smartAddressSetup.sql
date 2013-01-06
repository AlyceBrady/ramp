-
-- Table for address information.
--



DROP TABLE IF EXISTS Address;

CREATE TABLE Address (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    personID INT NOT NULL,
    addressType ENUM('PermanentHome', 'CurrentMailing', 'Billing',
        'PersonalEmail', 'WorkEmail', 'Phone') NOT NULL,
    address1 VARCHAR ( 40 ) NOT NULL,
    address2 VARCHAR ( 40 ),
    address3 VARCHAR ( 40 ),
    address4 VARCHAR ( 40 ),
    address5 VARCHAR ( 40 ),
    startDate DATE NOT NULL,
    endDate DATE NULL,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);

