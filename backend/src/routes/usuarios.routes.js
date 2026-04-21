import {Router} from 'express';
import {getUsuarios, 
    getUsuario, 
    createUsuario, 
    updateUsuario, 
    deleteUsuario,
    loginUsuario} from '../controllers/usuarios.controller.js';

const router = Router();

router.get('/usuario', getUsuarios);
router.get('/usuario/:id', getUsuario);
router.post('/usuario', createUsuario);
router.put('/usuario/:id', updateUsuario);
router.delete('/usuario/:id', deleteUsuario);
router.get('/login', loginUsuario);

export default router;