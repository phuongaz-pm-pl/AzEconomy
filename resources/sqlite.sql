-- #! sqlite
-- #{ az_economy
-- #    { init
CREATE TABLE IF NOT EXISTS az_economy (
    username TEXT,
    currencies TEXT
);
-- #      }
-- #      { select
-- #         :username string
SELECT * FROM az_economy WHERE username = :username;
-- #      }
-- #     { selects
SELECT * FROM az_economy;
-- #      }
-- #     { update
-- #          :username string
-- #          :currencies string
UPDATE az_economy SET currencies=:currencies WHERE username = :username;
-- #      }
-- #      { insert
-- #          :username string
-- #          :currencies string
INSERT INTO az_economy(username, currencies) VALUES (:username, :currencies);
-- #      }
-- #      { delete
-- #           :username string
DELETE FROM az_economy WHERE username = :username;
-- #      }
-- # }