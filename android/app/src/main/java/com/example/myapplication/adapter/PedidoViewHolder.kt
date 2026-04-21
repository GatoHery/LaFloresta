package com.example.myapplication.adapter

import android.view.View
import android.widget.TextView
import androidx.recyclerview.widget.RecyclerView
import com.example.myapplication.R
import com.example.myapplication.models.Pedido
import java.text.SimpleDateFormat
import java.util.Locale
import java.util.TimeZone

class PedidoViewHolder(view: View) : RecyclerView.ViewHolder(view) {

    private val fecha = view.findViewById<TextView>(R.id.textFecha)
    private val tipo = view.findViewById<TextView>(R.id.textTipo)
    private val nombre = view.findViewById<TextView>(R.id.textNombre)
    private val total = view.findViewById<TextView>(R.id.textTotal)
    private val mesera = view.findViewById<TextView>(R.id.textMesera)
    private val mesa = view.findViewById<TextView>(R.id.textMesa)

    fun render(pedido: Pedido) {
        val fechaFormateada = try {
            val formatoEntrada = SimpleDateFormat("yyyy-MM-dd'T'HH:mm:ss.SSS'Z'", Locale.US)
            formatoEntrada.timeZone = TimeZone.getTimeZone("UTC")
            val formatoSalida = SimpleDateFormat("dd/MM/yyyy", Locale.getDefault())
            val date = formatoEntrada.parse(pedido.getFecha())
            formatoSalida.format(date!!)
        } catch (e: Exception) {
            pedido.getFecha() ?: "Sin fecha"
        }

        val resumenProductos = pedido.getComidas()?.joinToString("\n") { item ->
            "${item.cantidad}x ${item.nombre} ($${String.format("%.2f", item.precio)})"
        } ?: "Sin productos"

        // TRUCO: Si el pedido no trae usuarioNombre, lo sacamos del primer item de comida
        val nombreMesera = pedido.getUsuarioNombre() ?: pedido.getComidas()?.firstOrNull()?.usuarioNombre ?: "Desconocido"

        fecha.text = "Fecha: $fechaFormateada"
        nombre.text = resumenProductos
        total.text = "Total: $${String.format("%.2f", pedido.getTotal())}"
        tipo.text = "Pedido #${pedido.getId()}"
        mesera.text = "Atendido por: $nombreMesera"
        mesa.text = "Mesa: ${pedido.getnMesas() ?: "N/A"}"
    }
}
