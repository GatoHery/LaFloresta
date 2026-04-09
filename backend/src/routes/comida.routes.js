import {Router} from 'express';
import {getComidas, 
    getComida, 
    createComida, 
    deleteComida, 
    updateComida} from '../controllers/comida.controllers.js';

const router = Router();

router.get('/comida', getComidas);

router.get('/comida/:id', getComida);

router.post('/comida', createComida);

router.delete('/comida/:id', deleteComida)

router.put('/comida/:id', updateComida)

export default router;