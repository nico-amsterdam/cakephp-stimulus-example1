-- POSTGRESQL

-- DROP USER organizer;

CREATE USER organizer WITH
  LOGIN
  CREATEDB
  CREATEROLE
  PASSWORD 'welcome123';

-- DROP SCHEMA contest CASCADE;

CREATE SCHEMA contest
    AUTHORIZATION organizer;

CREATE EXTENSION IF NOT EXISTS citext;

CREATE OR REPLACE FUNCTION update_modified_column() 
RETURNS TRIGGER AS $$
BEGIN
    NEW.modified = now();
    RETURN NEW; 
END;
$$ language 'plpgsql';


DROP TABLE IF EXISTS contest.contest CASCADE;

CREATE TABLE contest.contest
(
    id integer primary key generated always as identity,
    name text not null,
    number_of_prizes integer not null,
    prize1 text,
    prize2 text,
    prize3 text,
    created timestamptz not null default now(),
    modified timestamptz not null default now(),
    unique (name)
);

CREATE TRIGGER contest_moddatetime BEFORE UPDATE ON contest.contest FOR EACH ROW EXECUTE PROCEDURE update_modified_column();

ALTER TABLE contest.contest
    OWNER to organizer;

DROP TABLE IF EXISTS contest.participant CASCADE;

CREATE TABLE contest.participant
(
    id integer primary key generated always as identity,
    contest_id integer not null references contest.contest (id),
    name text not null,
    email citext,
    date_of_birth date,
    created timestamptz not null default now(),
    modified timestamptz not null default now(),
    unique (contest_id, name),
    unique (contest_id, email)
);


CREATE TRIGGER participant_moddatetime BEFORE UPDATE ON contest.participant FOR EACH ROW EXECUTE PROCEDURE update_modified_column();


ALTER TABLE contest.participant
    OWNER to organizer;
