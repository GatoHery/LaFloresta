import {pool} from '../db.js';

export const getPedidos = async (req, res) => {
    const {rows} = await pool.query(`
        SELECT p.id, p.fecha, p.total,
               c.id as comida_id, c.nombre, c.precio, pc.cantidad
        FROM pedido p 
        LEFT JOIN pedido_comida pc ON p.id = pc.pedido_id
        LEFT JOIN comida c ON pc.comida_id = c.id
        ORDER BY p.id
    `);
    
    // Agrupar comidas por pedido
    const pedidosMap = {};
    rows.forEach(row => {
        if (!pedidosMap[row.id]) {
            pedidosMap[row.id] = {
                id: row.id,
                fecha: row.fecha,
                total: row.total,
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
        SELECT p.id, p.fecha, p.total,
               c.id as comida_id, c.nombre, c.precio, pc.cantidad
        FROM pedido p 
        LEFT JOIN pedido_comida pc ON p.id = pc.pedido_id
        LEFT JOIN comida c ON pc.comida_id = c.id
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
    const {fecha, total, comidas} = req.body;
    
    // Validar que hay al menos una comida
    if (!comidas || comidas.length === 0) {
        return res.status(400).json({error: 'El pedido debe tener al menos una comida'});
    }
    
    try {
        // Crear el pedido
        const {rows: pedidoRows} = await pool.query(
            'INSERT INTO pedido (fecha, total) VALUES ($1, $2) RETURNING *', 
            [fecha, total]
        );
        
        const pedidoId = pedidoRows[0].id;
        
        // Validar que todas las comidas existen e insertar en pedido_comida
        const comidasInsertadas = [];
        
        for (const {comida_id, cantidad} of comidas) {
            const {rows: comidaExiste} = await pool.query('SELECT id FROM comida WHERE id = $1', [comida_id]);
            
            if (comidaExiste.length === 0) {
                return res.status(404).json({error: `Comida con id ${comida_id} no encontrada`});
            }
            
            // Insertar en pedido_comida
            const {rows: pcRows} = await pool.query(
                'INSERT INTO pedido_comida (pedido_id, comida_id, cantidad) VALUES ($1, $2, $3) RETURNING *',
                [pedidoId, comida_id, cantidad]
            );
            
            comidasInsertadas.push(pcRows[0]);
        }
        
        return res.json({
            ...pedidoRows[0],
            comidas: comidasInsertadas
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
    const {fecha, total, comidas} = req.body;
    
    try {
        // Verificar que el pedido existe
        const {rows: pedidoExiste} = await pool.query('SELECT id FROM pedido WHERE id = $1', [id]);
        
        if (pedidoExiste.length === 0) {
            return res.status(404).json({error: 'Pedido no encontrado'});
        }
        
        // Actualizar los datos del pedido
        const {rows: pedidoActualizado} = await pool.query(
            'UPDATE pedido SET fecha = $1, total = $2 WHERE id = $3 RETURNING *',
            [fecha, total, id]
        );
        
        // Si hay comidas, actualizar la tabla pedido_comida
        if (comidas && comidas.length > 0) {
            // Eliminar comidas anteriores
            await pool.query('DELETE FROM pedido_comida WHERE pedido_id = $1', [id]);
            
            // Insertar nuevas comidas
            const comidasInsertadas = [];
            
            for (const {comida_id, cantidad} of comidas) {
                const {rows: comidaExiste} = await pool.query('SELECT id FROM comida WHERE id = $1', [comida_id]);
                
                if (comidaExiste.length === 0) {
                    return res.status(404).json({error: `Comida con id ${comida_id} no encontrada`});
                }
                
                const {rows: pcRows} = await pool.query(
                    'INSERT INTO pedido_comida (pedido_id, comida_id, cantidad) VALUES ($1, $2, $3) RETURNING *',
                    [id, comida_id, cantidad]
                );
                
                comidasInsertadas.push(pcRows[0]);
            }
            
            return res.json({
                ...pedidoActualizado[0],
                comidas: comidasInsertadas
            });
        }
        
        return res.json(pedidoActualizado[0]);
    } catch (error) {
        res.status(500).json({error: error.message});
    }
}