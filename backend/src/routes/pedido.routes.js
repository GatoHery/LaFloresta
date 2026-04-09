import {Router} from 'express';
import {getPedidos, 
    getPedido, 
    createPedido, 
    deletePedido, 
    updatePedido} from '../controllers/pedido.controllers.js';

const router = Router();

router.get('/pedido', getPedidos);
router.get('/pedido/:id', getPedido);
router.post('/pedido', createPedido);
router.delete('/pedido/:id', deletePedido);
router.put('/pedido/:id', updatePedido);

export default router;