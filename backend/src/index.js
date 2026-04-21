import express from 'express';
import { PORT } from './config.js';
import ComidaRoutes from './routes/comida.routes.js';
import PedidoRoutes from './routes/pedido.routes.js';
import UsuarioRoutes from './routes/usuarios.routes.js';
import morgan from 'morgan';

const app = express();

app.use(morgan('dev'));
app.use(express.json());
app.use(ComidaRoutes);
app.use(PedidoRoutes);
app.use(UsuarioRoutes);

app.listen(PORT);
console.log('Server on port', PORT);