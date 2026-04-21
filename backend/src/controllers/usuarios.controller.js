import {pool} from '../db.js';

export const getUsuarios = async (req, res) => {
    const {rows} = await pool.query('SELECT * FROM usuario');
    res.json(rows);  
}

export const getUsuario = async (req, res) => {
    const {id} = req.params;
    const {rows} = await pool.query('SELECT * FROM usuario WHERE id = $1', [id]);
    
    if (rows.length === 0) {
        return res.status(404).json({error: 'Usuario no encontrado'});
    }
    
    res.json(rows[0]);
}

export const loginUsuario = async (req, res) => {
    // Usamos req.query porque en Android lo configuramos como @GET
    const { nombre, contrasena } = req.query; 

    try {
        const { rows } = await pool.query(
            'SELECT id, nombre, rol FROM usuario WHERE nombre = $1 AND contrasena = $2', 
            [nombre, contrasena]
        );

        if (rows.length === 0) {
            // 401 significa No Autorizado (Credenciales incorrectas)
            return res.status(401).json({ error: 'Usuario o contraseña incorrectos' });
        }

        // Si lo encuentra, devolvemos los datos del usuario
        res.json(rows[0]);
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
}

export const createUsuario = async (req, res) => {
    const data = req.body;
    const {rows} = await pool.query('INSERT INTO usuario (nombre, rol, contrasena) VALUES ($1, $2, $3) RETURNING *', [data.nombre, data.rol, data.contrasena]);
    res.json(rows[0]);
}

export const updateUsuario = async (req, res) => {
    const {id} = req.params;
    const data = req.body;
    const {rows} = await pool.query('UPDATE usuario SET nombre = $1, rol = $2, contrasena = $3 WHERE id = $4 RETURNING *', [data.nombre, data.rol, data.contrasena, id]);
    
    if (rows.length === 0) {
        return res.status(404).json({error: 'Usuario no encontrado'});
    }
    
    res.json(rows[0]);
}

export const deleteUsuario = async (req, res) => {
    const {id} = req.params;
    const {rowCount} = await pool.query('DELETE FROM usuario WHERE id = $1', [id]);
    
    if (rowCount === 0) {
        return res.status(404).json({error: 'Usuario no encontrado'});
    }
    
    res.sendStatus(204);
}