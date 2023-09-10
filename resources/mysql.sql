-- #!mysql
-- #{ init
CREATE TABLE IF NOT EXISTS player_data (
    uuid VARCHAR(255) NOT NULL,
    rank_id INT DEFAULT 0,
    coins INT DEFAULT 0,
    discord_id VARCHAR(255),
    tag INT DEFAULT 0,
    PRIMARY KEY (uuid)
);
-- #&
CREATE TABLE IF NOT EXISTS practice_data (
    uuid VARCHAR(255) NOT NULL,
	kills INT DEFAULT 0,
    deaths INT DEFAULT 0,
    wins INT DEFAULT 0,
    loses INT DEFAULT 0,
    elo INT DEFAULT 0,
    PRIMARY KEY (uuid)
);
-- #}

-- #{ createAccount
-- #    :uuid string
INSERT INTO player_data (uuid) VALUES (:uuid);
-- #&
ON DUPLICATE KEY UPDATE uuid = :uuid;
-- #}
-- #{ setCoins
-- #	:uuid string
-- #    :coins int
INSERT INTO player_data (uuid, coins) VALUES (:uuid, 0);
-- #&
ON DUPLICATE KEY UPDATE coins = :coins;
-- #}
-- #{ getCoins
-- #	:uuid string
SELECT coins FROM player_data WHERE uuid = :uuid;
-- #}
-- #{ setDiscord
-- #	:uuid string
-- #    :discord_id int
INSERT INTO player_data (uuid, discord_id) VALUES (:uuid, 0);
-- #&
ON DUPLICATE KEY UPDATE discord_id = :discord_id;
-- #}
-- #{ getDiscord
-- #	:uuid string
SELECT discord_id FROM player_data WHERE uuid = :uuid;
-- #}
-- #{ setRank
-- #	:uuid string
-- #    :rank_id int
INSERT INTO player_data (uuid, rank_id) VALUES (:uuid, 0)
-- #&
ON DUPLICATE KEY UPDATE rank_id = :rank_id;
-- #}
-- #{ getRank
-- #	:uuid string
SELECT rank_id FROM player_data WHERE uuid = :uuid;
-- #}
-- #{ setKills
-- #	:uuid string
-- #    :kills int
INSERT INTO practice_data (uuid, kills) VALUES (:uuid, 0)
-- #&
ON DUPLICATE KEY UPDATE kills = :kills;
-- #}
-- #{ getKills
-- #	:uuid string
SELECT kills FROM practice_data WHERE uuid = :uuid;
-- #}
-- #{ setWins
-- #	:uuid string
-- #    :wins int
INSERT INTO practice_data (uuid, wins) VALUES (:uuid, 0)
-- #&
ON DUPLICATE KEY UPDATE wins = :wins;
-- #}
-- #{ getWins
-- #	:uuid string
SELECT wins FROM practice_data WHERE uuid = :uuid;
-- #}
-- #{ setDeaths
-- #	:uuid string
-- #    :deaths int
INSERT INTO practice_data (uuid, deaths) VALUES (:uuid, 0)
-- #&
ON DUPLICATE KEY UPDATE deaths = :deaths;
-- #}
-- #{ getDeaths
-- #	:uuid string
SELECT deaths FROM practice_data WHERE uuid = :uuid;
-- #}
-- #{ setLoses
-- #	:uuid string
-- #    :loses int
INSERT INTO practice_data (uuid, loses) VALUES (:uuid, 0)
-- #&
ON DUPLICATE KEY UPDATE loses = :loses;
-- #}
-- #{ getLoses
-- #	:uuid string
SELECT loses FROM practice_data WHERE uuid = :uuid;
-- #}
-- #{ setElo
-- #	:uuid string
-- #    :elo int
INSERT INTO practice_data (uuid, elo) VALUES (:uuid, 0)
--  #&
ON DUPLICATE KEY UPDATE elo = :elo;
-- #}
-- #{ getElo
-- #	:uuid string
SELECT elo FROM practice_data WHERE uuid = :uuid;
-- #}