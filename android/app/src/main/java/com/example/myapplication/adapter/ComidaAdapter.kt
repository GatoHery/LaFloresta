package com.example.myapplication.adapter

import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.recyclerview.widget.RecyclerView
import com.example.myapplication.R
import com.example.myapplication.models.Comida

class ComidaAdapter(
    private var comidaList: List<Comida>,
    private val onComidaClick: (Comida) -> Unit
) : RecyclerView.Adapter<ComidaViewHolder>() {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ComidaViewHolder {
        val layoutInflater = LayoutInflater.from(parent.context)
        return ComidaViewHolder(layoutInflater.inflate(R.layout.item_comida, parent, false))
    }

    override fun onBindViewHolder(holder: ComidaViewHolder, position: Int) {
        val item = comidaList[position]
        holder.render(item)
        holder.itemView.setOnClickListener { onComidaClick(item) }
    }

    override fun getItemCount(): Int = comidaList.size

    fun updateList(newList: List<Comida>) {
        comidaList = newList
        notifyDataSetChanged()
    }
}
