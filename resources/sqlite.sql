-- #!sqlite
-- #{ init
CREATE TABLE IF NOT EXISTS player_data (
	uuid TEXT PRIMARY KEY,
	rank_id INTEGER DEFAULT 0,
	discord_id INT DEFAULT 0,
	tag_id INT DEFAULT 0,
	particle_id INT DEFAULT 0,
	particle_color INT DEFAULT 0,
	current_cape TEXT DEFAULT "",
	tags_owned TEXT DEFAULT "",
	cape_owned TEXT DEFAULT "",
	particle_owned TEXT DEFAULT "",
	always_sprinting BOOLEAN DEFAULT FALSE,
	players_visible BOOLEAN DEFAULT TRUE
);
-- #&
CREATE INDEX IF NOT EXISTS player_data_tag ON player_data (tag_id);
-- #&
CREATE INDEX IF NOT EXISTS player_data_rank ON player_data (rank_id);
-- #&
CREATE INDEX IF NOT EXISTS player_data_discord ON player_data (discord_id);
-- #&
CREATE INDEX IF NOT EXISTS player_data_id ON player_data (tag_id);
-- #&
CREATE INDEX IF NOT EXISTS player_data_particle_id ON player_data (particle_id);
-- #&
CREATE INDEX IF NOT EXISTS player_data_particle_color ON player_data (particle_color);
-- #&
CREATE INDEX IF NOT EXISTS player_data_current_cape ON player_data (current_cape);
-- #&
CREATE INDEX IF NOT EXISTS player_data_tags_owned ON player_data (tags_owned);
-- #&
CREATE INDEX IF NOT EXISTS player_data_cape_owned ON player_data (cape_owned);
-- #&
CREATE INDEX IF NOT EXISTS player_data_particle_owned ON player_data (particle_owned);
-- #&
CREATE INDEX IF NOT EXISTS player_data_always_sprinting ON player_data (always_sprinting);
-- #&
CREATE INDEX IF NOT EXISTS player_data_players_visible ON player_data (players_visible);
-- #}
-- #{ createAccount
-- #	:uuid string
INSERT OR IGNORE INTO player_data (uuid, rank_id, discord_id, current_cape, tag_id, particle_id, particle_color, tags_owned, cape_owned, particle_owned, always_sprinting, players_visible) VALUES (:uuid, 0, NULL, 0, "", 0, 0, 0, "", "", false, true)
-- #&
UPDATE player_data SET uuid = :uuid;
-- #}
-- #{ checkAccount
-- #    :uuid string
SELECT uuid FROM player_data WHERE uuid = :uuid;
-- #}
-- #{ setTag
-- #	:uuid string
-- #    :tag_id int
INSERT OR IGNORE INTO player_data (uuid, tag_id) VALUES (:tag_id, 0);
-- #&
UPDATE player_data SET tag_id = :tag_id WHERE uuid = :uuid;
-- #}
-- #{ getTag
-- #	:uuid string
SELECT tag_id FROM player_data WHERE uuid = :uuid;
-- #}
-- #{ setDiscord
-- #	:uuid string
-- #    :discord_id string
INSERT OR IGNORE INTO player_data (uuid, discord_id) VALUES (:discord_id, 0);
-- #&
UPDATE player_data SET discord_id = :discord_id WHERE uuid = :uuid;
-- #}
-- #{ getDiscord
-- #	:uuid string
SELECT discord_id FROM player_data WHERE uuid = :uuid;
-- #}
-- #{ setRank
-- #	:uuid string
-- #    :rank_id int
INSERT OR IGNORE INTO player_data (uuid, rank_id) VALUES (:uuid, 0);
-- #&
UPDATE player_data SET rank_id = :rank_id WHERE uuid = :uuid;
-- #}
-- #{ getRank
-- #	:uuid string
SELECT rank_id FROM player_data WHERE uuid = :uuid;
-- #}
-- #{ setTagsOwned
-- #	:uuid string
-- #    :tags_owned string
INSERT OR IGNORE INTO player_data (uuid, tags_owned) VALUES (:uuid, "");
-- #&
UPDATE player_data SET tags_owned = :tags_owned WHERE uuid = :uuid;
-- #}
-- #{ getTagsOwned
-- #	:uuid string
SELECT tags_owned FROM player_data WHERE uuid = :uuid;
-- #}
-- #{ setCapeOwned
-- #	:uuid string
-- #    :cape_owned string
INSERT OR IGNORE INTO player_data (uuid, cape_owned) VALUES (:uuid, 0);
-- #&
UPDATE player_data SET cape_owned = :cape_owned WHERE uuid = :uuid;
-- #}
-- #{ getCapeOwned
-- #	:uuid string
SELECT cape_owned FROM player_data WHERE uuid = :uuid;
-- #}
-- #{ setAlwaysSprinting
-- #	:uuid string
-- #    :confirm bool
INSERT OR IGNORE INTO player_data (uuid, always_sprinting) VALUES (:uuid, false);
-- #&
UPDATE player_data SET always_sprinting = :confirm WHERE uuid = :uuid;
-- #}
-- #{ getAlwaysSprinting
-- #	:uuid string
SELECT always_sprinting FROM player_data WHERE uuid = :uuid;
-- #}
-- #{ setPlayerVisibility
-- #	:uuid string
-- #    :confirm bool
INSERT OR IGNORE INTO player_data (uuid, players_visible) VALUES (:uuid, true);
-- #&
UPDATE player_data SET players_visible = :confirm WHERE uuid = :uuid;
-- #}
-- #{ getPlayerVisibility
-- #	:uuid string
SELECT players_visible FROM player_data WHERE uuid = :uuid;
-- #}
-- #{ setActiveParticle
-- #	:uuid string
-- #    :particle_id int
INSERT OR IGNORE INTO player_data (uuid, particle_id) VALUES (:uuid, 0);
-- #&
UPDATE player_data SET particle_id = :particle_id WHERE uuid = :uuid;
-- #}
-- #{ getActiveParticle
-- #	:uuid string
SELECT particle_id FROM player_data WHERE uuid = :uuid;
-- #}
-- #{ setParticleColor
-- #	:uuid string
-- #    :particle_color int
INSERT OR IGNORE INTO player_data (uuid, particle_color) VALUES (:uuid, 0);
-- #&
UPDATE player_data SET particle_color = :particle_color WHERE uuid = :uuid;
-- #}
-- #{ getParticleColor
-- #	:uuid string
SELECT particle_color FROM player_data WHERE uuid = :uuid;
-- #}
-- #{ setParticlesOwned
-- #	:uuid string
-- #    :particle_owned int
INSERT OR IGNORE INTO player_data (uuid, particle_owned) VALUES (:uuid, 0);
-- #&
UPDATE player_data SET particle_owned = :particle_owned WHERE uuid = :uuid;
-- #}
-- #{ getParticlesOwned
-- #	:uuid string
SELECT particle_owned FROM player_data WHERE uuid = :uuid;
-- #}