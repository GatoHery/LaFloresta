import {pool} from '../db.js';

export const getComidas = async (req, res) => {
    const {rows} = await pool.query('SELECT * FROM comida');
    res.json(rows);  
}

export const getComida = async (req, res) => {
    const {id} = req.params;
    const {rows} = await pool.query('SELECT * FROM comida WHERE id = $1', [id]);
   
    if (rows.length === 0) {
        return res.status(404).json({error: 'Comida no encontrada'});
    }
    return res.json(rows);
}

export const createComida = async (req, res) => {
    const data = req.body;
    const {rows} = await pool.query('INSERT INTO comida (nombre, precio,tipo) VALUES ($1, $2, $3) RETURNING *', [data.nombre, data.precio, data.tipo]);
    return res.json(rows[0]);
}

export const deleteComida = async (req, res) => {
    const {id} = req.params;
    const {rowCount} = await pool.query('SELECT * FROM comida WHERE id = $1 RETURNING *', [id]); 

    if (rowCount === 0) {
        return res.status(404).json({error: 'Comida no encontrada'});
    }

    return res.sendStatus(204);
}

export const updateComida = async (req, res) => {
    const {id} = req.params;
    const data = req.body;

    const {rows} = await pool.query('UPDATE comida SET nombre = $1, precio = $2, tipo = $3 WHERE id = $4 RETURNING *', [data.nombre, data.precio, data.tipo, id]);

    return res.json(rows[0]);
}