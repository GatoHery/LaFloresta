package com.example.myapplication.adapterimport

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.ImageButton
import android.widget.TextView
import androidx.recyclerview.widget.RecyclerView
import com.example.myapplication.R
import com.example.myapplication.models.PedidoComida // <--- Cambiado

class PedidoTemporalAdapter(
    private val items: MutableList<PedidoComida>, // <--- Cambiado
    private val onEliminarClick: (Int) -> Unit
) : RecyclerView.Adapter<PedidoTemporalAdapter.ViewHolder>() {

    class ViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        val tvNombre: TextView = view.findViewById(R.id.tvNombreTemp)
        val tvDetalle: TextView = view.findViewById(R.id.tvDetalleTemp)
        val btnEliminar: ImageButton = view.findViewById(R.id.btnEliminar)
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val view = LayoutInflater.from(parent.context)
            .inflate(R.layout.item_pedido_temporal, parent, false)
        return ViewHolder(view)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        val item = items[position]
        holder.tvNombre.text = item.nombre
        // Calculamos el subtotal del item: precio * cantidad
        val subtotalItem = (item.precio ?: 0f) * (item.cantidad ?: 0)
        holder.tvDetalle.text = "Cant: ${item.cantidad} - Subtotal: $${String.format("%.2f", subtotalItem)}"

        holder.btnEliminar.setOnClickListener {
            onEliminarClick(position)
        }
    }

    override fun getItemCount(): Int = items.size
}