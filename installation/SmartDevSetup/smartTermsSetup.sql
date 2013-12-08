--
-- Table for information about academic terms.
--


USE `smart_dev`;

-- Before dropping Terms, need to drop table(s) that depend on it.
SOURCE dropTermModuleDependencies.sql

DROP TABLE IF EXISTS Terms;

CREATE TABLE Terms (
    term VARCHAR( 10 ) NOT NULL PRIMARY KEY,
    acadYear VARCHAR( 10 ),
    startDate DATE NOT NULL,
    censusDate DATE NOT NULL,
    endDate DATE NOT NULL,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO Terms (acadYear, term, startDate, censusDate, endDate)
VALUES
('2008-09', '2008Fall', '2008-09-10', '2008-09-15', '2008-12-15')
, ('2008-09', '2009Winter', '2009-01-01', '2009-01-05', '2009-03-20')
, ('2008-09', '2009Spring', '2009-04-01', '2009-04-05', '2009-06-20')
, ('2009-10', '2009Fall', '2009-09-10', '2009-09-15', '2009-12-15')
, ('2009-10', '2010Q1', '2010-01-01', '2010-01-05', '2010-03-20')
, ('2009-10', '2010Q2', '2010-04-01', '2010-04-05', '2010-06-20')
, ('2010-11', '2010Q4', '2010-09-10', '2010-09-15', '2010-12-15')
, ('2010-11', '2011Q1', '2011-01-01', '2011-01-05', '2011-03-20')
, ('2010-11', '2011Q2', '2011-04-01', '2011-04-05', '2011-06-20')
, ('2011-12', '2011Q4', '2011-09-10', '2011-09-15', '2011-12-15')
, ('2011-12', '2012Q1', '2012-01-01', '2012-01-05', '2012-03-20')
, ('2011-12', '2012Q2', '2012-04-01', '2012-04-05', '2012-06-20')
, ('2012-13', '2012Q4', '2012-09-10', '2012-09-15', '2012-12-15')
;

