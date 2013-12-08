-
-- Tables for information about academic program requirements.
--


USE `smart_dev`;

DROP TABLE IF EXISTS Requirements;

CREATE TABLE Requirements (
    requirementID INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    requirementName VARCHAR ( 30 ) NOT NULL,
    parentRequirementID INT NULL,
    startDate DATE NOT NULL,
    endDate DATE,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parentRequirementID) REFERENCES Requirements (requirementID)
        ON UPDATE CASCADE ON DELETE RESTRICT,
);

DROP TABLE IF EXISTS ProgramRequirements;

CREATE TABLE ProgramRequirements (
    programID INT NOT NULL,
    requirementID INT NOT NULL,
    startDate DATE NOT NULL,
    endDate DATE,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (programID, requirementID, startDate)
    FOREIGN KEY (programID) REFERENCES Program (programID),
    FOREIGN KEY (requirementID) REFERENCES Requirements (requirementID)
);

# /*
# Start date is part of primary key in case of a rare situation
# where a program is associated with requirement set 1 for a while,
# then with requirement set 2, then back with requirement set 1.
# Program requirements should only change at clean boundaries,
# e.g., the start of an academic year.
# A program should only have one active requirement set at any one
# time.  There might be different students complete different
# requirements, based on the requirement set that was active when
# they started the program, but there should be no ambiguity about
# the requirements associated with a particular program at a particular
# date.
# */


DROP TABLE IF EXISTS Catalog

CREATE TABLE Catalog (
    catalogID INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    catalogName VARCHAR ( 10 ) NOT NULL,
    entity VARCHAR (30),
    startDate DATE NOT NULL,
    endDate DATE NOT NULL,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);

DROP TABLE IF EXISTS CatalogReqMapping;

CREATE TABLE CatalogReqMapping (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    catalogID INT NOT NULL,
    requirementSet INT NOT NULL,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);

# /*
# Catalog copy: from oldCatalog to newCatalog
# Copy all the requirement mappings with oldCatalog to mappings with
# newCatalog.  Do not have to copy requirements or requirement sets!
# */

# /*
# Need new design -- didn't I have a new table for every type of requirement?
# That does not scale!
# What I have below is incomplete: a requirement set has to be a list of
# requirements, e.g., ReqSetNameA, Requirement1
#                     ReqSetNameA, Requirement2
#                     ReqSetNameB, Requirement1ForIt
# I used this idea, but for ands and ors within a single requirement, i.e.,
# the ReqSetName column was a sub-requirement for requirement set represented
# by table (I think) -- example above would be ReqSet: (A1 or A2) and B1
# */
DROP TABLE IF EXISTS ReqSet;

CREATE TABLE ReqSet (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    requirementSetName VARCHAR ( 20 ),
    reqSetID INT NULL DEFAULT NULL;
    requirementID INT NULL DEFAULT NULL;
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (reqSetID) REFERENCES ReqSet (pk_id),
    FOREIGN KEY (requirementID) REFERENCES Requirement (pk_id)
);

# /*
# Is there a way to specify that only one of reqSetID or requirementID can
# be non-NULL?
# */

DROP TABLE IF EXISTS Requirement;

CREATE TABLE Requirement (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    category VARCHAR ( 20 ),
    requirementName VARCHAR ( 20 ),
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    INDEX (category)
);

# /*
# Requirement will be something like a count of things, and then
# a specification of what the "things" are: e.g., take course X 1 time,
# take 3 courses with attribute combination (department Y and level Z).
# How to handle attribute combinations?  Is this just count of 3 for
# requirement set (department Y and level Z)?
# */

# /*
# See notes on the impact of changing a requirement (maybe never happens,
# always create new requirements?) and changing the attributes associated
# modules.
# */
