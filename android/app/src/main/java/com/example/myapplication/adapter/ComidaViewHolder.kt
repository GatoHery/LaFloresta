package com.example.myapplication.adapter

import android.view.View
import android.widget.TextView
import androidx.recyclerview.widget.RecyclerView
import com.example.myapplication.R
import com.example.myapplication.models.Comida

class ComidaViewHolder(view: View) : RecyclerView.ViewHolder(view) {
    val nombre = view.findViewById<TextView>(R.id.textNombreComida)
    val precio = view.findViewById<TextView>(R.id.textPrecioComida)
    val tipo = view.findViewById<TextView>(R.id.textTipoComida)

    fun render(comidaModel: Comida){
        nombre.text = comidaModel.nombre
        precio.text = "$${comidaModel.precio}"
        tipo.text = comidaModel.tipo
    }
}
