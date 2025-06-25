CREATE TABLE kriteria (
    id SERIAL PRIMARY KEY,
    nama VARCHAR(100),
    bobot DOUBLE PRECISION,
    tipe VARCHAR(10) CHECK (tipe IN ('benefit', 'cost'))
);

CREATE TABLE sub_kriteria (
    id SERIAL PRIMARY KEY,
    id_kriteria INT REFERENCES kriteria(id),
    nama VARCHAR(100),
    bobot DOUBLE PRECISION
);

CREATE TABLE alternatif (
    id SERIAL PRIMARY KEY,
    nama VARCHAR(100)
);

CREATE TABLE nilai (
    id SERIAL PRIMARY KEY,
    id_alternatif INT REFERENCES alternatif(id),
    id_sub_kriteria INT REFERENCES sub_kriteria(id),
    nilai DOUBLE PRECISION
);
