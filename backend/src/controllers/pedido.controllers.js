import {pool} from '../db.js';

export const getPedidos = async (req, res) => {
    const {usuario_id} = req.query;
    
    let query = `
        SELECT p.id, p.fecha, p.total, u.nombre as usuario_nombre,
               c.id as comida_id, c.nombre, c.precio, pcu.cantidad, pcu.n_mesas
        FROM pedido p 
        LEFT JOIN pedido_comida_usuario pcu ON p.id = pcu.pedido_id
        LEFT JOIN usuario u ON pcu.usuario_id = u.id
        LEFT JOIN comida c ON pcu.comida_id = c.id
    `;
    
    if (usuario_id) {
        query += ` WHERE pcu.usuario_id = $1 `;
    }
    
    query += ` ORDER BY p.id `;
    
    const {rows} = await pool.query(query, usuario_id ? [usuario_id] : [])
    
    // Agrupar comidas por pedido
    const pedidosMap = {};
    rows.forEach(row => {
        if (!pedidosMap[row.id]) {
            pedidosMap[row.id] = {
                id: row.id,
                fecha: row.fecha,
                total: row.total,
                usuario_nombre: row.usuario_nombre,
                n_mesas: row.n_mesas,
                comidas: []
            };
        }
        if (row.comida_id) {
            pedidosMap[row.id].comidas.push({
                comida_id: row.comida_id,
                nombre: row.nombre,
                precio: row.precio,
                cantidad: row.cantidad
            });
        }
    });
    
    res.json(Object.values(pedidosMap));  
}

export const getPedido = async (req, res) => {
    const {id} = req.params;
    const {rows} = await pool.query(`
        SELECT p.id, p.fecha, p.total, u.nombre as usuario_nombre,
               c.id as comida_id, c.nombre, c.precio, pcu.cantidad, pcu.n_mesas
        FROM pedido p 
        LEFT JOIN pedido_comida_usuario pcu ON p.id = pcu.pedido_id
        LEFT JOIN usuario u ON pcu.usuario_id = u.id
        LEFT JOIN comida c ON pcu.comida_id = c.id
        WHERE p.id = $1
    `, [id]);
    
    if (rows.length === 0) {
        return res.status(404).json({error: 'Pedido no encontrado'});
    }
    
    // Estructurar la respuesta
    const pedido = {
        id: rows[0].id,
        fecha: rows[0].fecha,
        total: rows[0].total,
        usuario_nombre: rows[0].usuario_nombre,
        n_mesas: rows[0].n_mesas,
        comidas: rows.filter(r => r.comida_id).map(r => ({
            comida_id: r.comida_id,
            nombre: r.nombre,
            precio: r.precio,
            cantidad: r.cantidad
        }))
    };
    
    return res.json(pedido);
}

export const createPedido = async (req, res) => {
    const {fecha, total, n_mesas, comidas} = req.body;
    
    // Validar que hay al menos una comida
    if (!comidas || comidas.length === 0) {
        return res.status(400).json({error: 'El pedido debe tener al menos una comida'});
    }
    
    // Validar que n_mesas está presente
    if (!n_mesas) {
        return res.status(400).json({error: 'n_mesas es requerido'});
    }
    
    try {
        // Crear el pedido
        const {rows: pedidoRows} = await pool.query(
            'INSERT INTO pedido (fecha, total) VALUES ($1, $2) RETURNING *', 
            [fecha, total]
        );
        
        const pedidoId = pedidoRows[0].id;
        
        // Validar que todas las comidas existen e insertar en pedido_comida_usuario
        const comidasInsertadas = [];
        
        for (const {usuario_id, comida_id, cantidad} of comidas) {
            // Validar que el usuario existe
            const {rows: usuarioExiste} = await pool.query('SELECT id FROM usuario WHERE id = $1', [usuario_id]);
            if (usuarioExiste.length === 0) {
                return res.status(404).json({error: `Usuario con id ${usuario_id} no encontrado`});
            }
            
            // Validar que la comida existe
            const {rows: comidaExiste} = await pool.query('SELECT id FROM comida WHERE id = $1', [comida_id]);
            
            if (comidaExiste.length === 0) {
                return res.status(404).json({error: `Comida con id ${comida_id} no encontrada`});
            }
            
            // Insertar en pedido_comida_usuario
            const {rows: pcuRows} = await pool.query(
                'INSERT INTO pedido_comida_usuario (usuario_id, pedido_id, comida_id, cantidad, n_mesas) VALUES ($1, $2, $3, $4, $5) RETURNING *',
                [usuario_id, pedidoId, comida_id, cantidad, n_mesas]
            );
            
            comidasInsertadas.push(pcuRows[0]);
        }
        
        return res.json({
            ...pedidoRows[0],
            n_mesas: n_mesas,
            comidas: comidasInsertadas.map(c => ({
                usuario_id: c.usuario_id,
                comida_id: c.comida_id,
                cantidad: c.cantidad
            }))
        });
    } catch (error) {
        res.status(500).json({error: error.message});
    }
}

export const deletePedido = async (req, res) => {
    const {id} = req.params;
    const {rowCount} = await pool.query('DELETE FROM pedido WHERE id = $1 RETURNING *', [id]);
    
    if (rowCount === 0) {
        return res.status(404).json({error: 'Pedido no encontrado'});
    }

    return res.sendStatus(204);
}

export const updatePedido = async (req, res) => {
    const {id} = req.params;
    const {fecha, total, n_mesas, comidas} = req.body;
    
    try {
        // Verificar que el pedido existe
        const {rows: pedidoExiste} = await pool.query('SELECT id FROM pedido WHERE id = $1', [id]);
        
        if (pedidoExiste.length === 0) {
            return res.status(404).json({error: 'Pedido no encontrado'});
        }
        
        // Validar que n_mesas está presente si hay comidas
        if (comidas && comidas.length > 0 && !n_mesas) {
            return res.status(400).json({error: 'n_mesas es requerido cuando hay comidas'});
        }
        
        // Actualizar los datos del pedido
        const {rows: pedidoActualizado} = await pool.query(
            'UPDATE pedido SET fecha = $1, total = $2 WHERE id = $3 RETURNING *',
            [fecha, total, id]
        );
        
        // Si hay comidas, actualizar la tabla pedido_comida_usuario
        if (comidas && comidas.length > 0) {
            // Eliminar comidas anteriores
            await pool.query('DELETE FROM pedido_comida_usuario WHERE pedido_id = $1', [id]);
            
            // Insertar nuevas comidas
            const comidasInsertadas = [];
            
            for (const {usuario_id, comida_id, cantidad} of comidas) {
                // Validar que el usuario existe
                const {rows: usuarioExiste} = await pool.query('SELECT id FROM usuario WHERE id = $1', [usuario_id]);
                if (usuarioExiste.length === 0) {
                    return res.status(404).json({error: `Usuario con id ${usuario_id} no encontrado`});
                }
                
                // Validar que la comida existe
                const {rows: comidaExiste} = await pool.query('SELECT id FROM comida WHERE id = $1', [comida_id]);
                
                if (comidaExiste.length === 0) {
                    return res.status(404).json({error: `Comida con id ${comida_id} no encontrada`});
                }
                
                const {rows: pcuRows} = await pool.query(
                    'INSERT INTO pedido_comida_usuario (usuario_id, pedido_id, comida_id, cantidad, n_mesas) VALUES ($1, $2, $3, $4, $5) RETURNING *',
                    [usuario_id, id, comida_id, cantidad, n_mesas]
                );
                
                comidasInsertadas.push(pcuRows[0]);
            }
            
            return res.json({
                ...pedidoActualizado[0],
                n_mesas: n_mesas,
                comidas: comidasInsertadas.map(c => ({
                    usuario_id: c.usuario_id,
                    comida_id: c.comida_id,
                    cantidad: c.cantidad
                }))
            });
        }
        
        return res.json(pedidoActualizado[0]);
    } catch (error) {
        res.status(500).json({error: error.message});
    }
}