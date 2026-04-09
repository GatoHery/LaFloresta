import pg from 'pg';

export const pool = new pg.Pool({
    user: "postgres",
    host: "localhost",
    password: "Tortuga01",
    database: "floresta",
    port: "5432",
})
