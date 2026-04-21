package com.example.myapplication.adapter

import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.recyclerview.widget.RecyclerView
import com.example.myapplication.R
import com.example.myapplication.models.Pedido

class PedidoAdapter(
    private val pedidoList: MutableList<Pedido>,
    private val onEditClick: (Pedido) -> Unit
) : RecyclerView.Adapter<PedidoViewHolder>() {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): PedidoViewHolder {
        val layoutInflater = LayoutInflater.from(parent.context)
        return PedidoViewHolder(layoutInflater.inflate(R.layout.item_pedido, parent, false))
    }

    override fun onBindViewHolder(holder: PedidoViewHolder, position: Int) {
        val item = pedidoList[position]
        holder.render(item)
        
        // Configuramos el click del botón editar desde aquí o pasamos el callback al VH
        holder.itemView.findViewById<android.view.View>(R.id.btnEditarPedido).setOnClickListener {
            onEditClick(item)
        }
    }

    override fun getItemCount(): Int = pedidoList.size

    fun addItems(newItems: List<Pedido>) {
        val startPosition = pedidoList.size
        pedidoList.addAll(newItems)
        notifyItemRangeInserted(startPosition, newItems.size)
    }
}
