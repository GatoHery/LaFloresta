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

    fun render(pedido: Pedido) {
        // 1. Formatear la fecha
        val fechaFormateada = try {
            val formatoEntrada = SimpleDateFormat("yyyy-MM-dd'T'HH:mm:ss.SSS'Z'", Locale.US)
            formatoEntrada.timeZone = TimeZone.getTimeZone("UTC")
            val formatoSalida = SimpleDateFormat("dd/MM/yyyy", Locale.getDefault())
            val date = formatoEntrada.parse(pedido.fecha)
            formatoSalida.format(date!!)
        } catch (e: Exception) {
            pedido.fecha ?: "Sin fecha"
        }

        // 2. Crear un resumen de los productos (ej: "2x Pizza, 1x Soda")
        val resumenProductos = pedido.comidas?.joinToString("\n") { item ->
            // Aquí agregamos el precio de cada item
            "${item.cantidad}x ${item.nombre} ($${String.format("%.2f", item.precio)})"
        } ?: "Sin productos"

        // 3. Asignar valores a la interfaz
        fecha.text = "Fecha: $fechaFormateada"
        nombre.text = resumenProductos
        total.text = "Total: $${String.format("%.2f", pedido.total)}"

        // 4. Limpiar o reutilizar campos antiguos
        tipo.text = "Pedido #${pedido.id}"
    }
}