-- Notes Table SQL

CREATE TABLE notes (
    
    id          INT             NOT NULL  AUTO_INCREMENT,
    title       VARCHAR(255)    NOT NULL,    
    date        VARCHAR(100)    NOT NULL,
    body        TEXT,
    PRIMARY KEY (id)
    
);


-- Reviews Table SQL

CREATE TABLE reviews (
    
    id          INT             NOT NULL  AUTO_INCREMENT,
    date        VARCHAR(100)    NOT NULL,
    page        VARCHAR(255)    NOT NULL,    
    designer    VARCHAR(100)    NOT NULL,    
    reviewer    VARCHAR(100)    NOT NULL,    
    scorecard   TEXT            NOT NULL,
    score       INT,
    PRIMARY KEY (id)
    
);


