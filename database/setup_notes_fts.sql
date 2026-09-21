-- First, install the pgroonga extension on your PostgreSQL server with:
-- sudo apt-get install postgresql-15-pgroonga
-- or the equivalent for your operating system;
CREATE EXTENSION IF NOT EXISTS pgroonga WITH SCHEMA public;

CREATE INDEX search_indexes_pgroonga_idx ON search_indexes USING pgroonga (content);
