package com.example.myapplication.Navegation

import android.os.Bundle
import android.util.Log
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.fragment.app.Fragment
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.example.myapplication.R
import com.example.myapplication.adapter.PedidoAdapter
import com.example.myapplication.interfaces.ComidaAPI
import com.example.myapplication.models.Pedido
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response
import retrofit2.Retrofit
import retrofit2.converter.gson.GsonConverterFactory

class PedidosFragment : Fragment() {

    private lateinit var recyclerView: RecyclerView

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        val view = inflater.inflate(R.layout.fragment_pedidos, container, false)

        recyclerView = view.findViewById(R.id.recycler_pedidos)
        recyclerView.layoutManager = LinearLayoutManager(requireContext())

        obtenerTodosLosPedidos()

        return view
    }

    private fun obtenerTodosLosPedidos() {
        val retrofit = Retrofit.Builder()
            .baseUrl("http://10.0.2.2:3000/")
            .addConverterFactory(GsonConverterFactory.create())
            .build()

        val api = retrofit.create(ComidaAPI::class.java)

        api.getPedidos().enqueue(object : Callback<List<Pedido>> {
            override fun onResponse(call: Call<List<Pedido>>, response: Response<List<Pedido>>) {
                if (response.isSuccessful) {
                    val lista = response.body() ?: emptyList()
                    recyclerView.adapter = PedidoAdapter(lista)
                }
            }

            override fun onFailure(call: Call<List<Pedido>>, t: Throwable) {
                Log.e("API_ERROR", t.message.toString())
            }
        })
    }
}
