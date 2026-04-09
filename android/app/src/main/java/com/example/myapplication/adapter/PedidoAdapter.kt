package com.example.myapplication.adapter

import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.recyclerview.widget.RecyclerView
import com.example.myapplication.R
import com.example.myapplication.models.Pedido

class PedidoAdapter(private val pedidoList: List<Pedido>) : RecyclerView.Adapter<PedidoViewHolder>() {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): PedidoViewHolder {
        val layoutInflater = LayoutInflater.from(parent.context)
        return PedidoViewHolder(layoutInflater.inflate(R.layout.item_pedido, parent, false))
    }

    override fun onBindViewHolder(holder: PedidoViewHolder, position: Int) {
        holder.render(pedidoList[position])
    }

    override fun getItemCount(): Int = pedidoList.size
}