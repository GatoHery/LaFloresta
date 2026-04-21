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
import com.example.myapplication.adapter.ComidaAdapter
import com.example.myapplication.interfaces.ComidaAPI
import com.example.myapplication.models.Comida
import okhttp3.OkHttpClient
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response
import retrofit2.Retrofit
import retrofit2.converter.gson.GsonConverterFactory

class HomeFragment : Fragment() {

    private lateinit var recyclerView: RecyclerView

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        val view = inflater.inflate(R.layout.fragment_home, container, false)

        recyclerView = view.findViewById(R.id.recycler_comida)
        recyclerView.layoutManager = LinearLayoutManager(requireContext())

        obtenerTodasLasComidas()

        return view
    }

    private fun obtenerTodasLasComidas() {
        val client = OkHttpClient.Builder().addInterceptor { chain ->
            val request = chain.request().newBuilder()
                .addHeader("Accept", "application/json")
                .build()
            chain.proceed(request)
        }.build()

        val retrofit = Retrofit.Builder()
            .baseUrl("http://10.0.2.2:8000/api/")
            .client(client)
            .addConverterFactory(GsonConverterFactory.create())
            .build()

        val api = retrofit.create(ComidaAPI::class.java)

        api.getAll().enqueue(object : Callback<List<Comida>> {
            override fun onResponse(call: Call<List<Comida>>, response: Response<List<Comida>>) {
                if (response.isSuccessful) {
                    val lista = response.body() ?: emptyList()
                    recyclerView.adapter = ComidaAdapter(lista) {
                        // Acción al hacer clic, por ahora vacía
                    }
                }
            }

            override fun onFailure(call: Call<List<Comida>>, t: Throwable) {
                Log.e("API_ERROR", t.message.toString())
            }
        })
    }
}
