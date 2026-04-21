package com.example.myapplication.Navegation

import android.content.Context
import android.os.Bundle
import android.util.Log
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Button
import android.widget.EditText
import android.widget.Toast
import androidx.fragment.app.Fragment
import androidx.navigation.fragment.findNavController
import com.example.myapplication.R
import com.example.myapplication.interfaces.ComidaAPI
import com.example.myapplication.models.Usuario
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response
import retrofit2.Retrofit
import retrofit2.converter.gson.GsonConverterFactory

class LoginFragment : Fragment() {

    override fun onCreateView(inflater: LayoutInflater, container: ViewGroup?, savedInstanceState: Bundle?): View? {
        val view = inflater.inflate(R.layout.fragment_login, container, false)

        val etNombre = view.findViewById<EditText>(R.id.etNombreUsuario)
        val etContra = view.findViewById<EditText>(R.id.etContrasena)
        val btnLogin = view.findViewById<Button>(R.id.btnLogin)

        btnLogin.setOnClickListener {
            val nombre = etNombre.text.toString()
            val contra = etContra.text.toString()

            if (nombre.isNotEmpty() && contra.isNotEmpty()) {
                realizarLogin(nombre, contra)
            } else {
                Toast.makeText(requireContext(), "Completa todos los campos", Toast.LENGTH_SHORT).show()
            }
        }

        return view
    }

    private fun realizarLogin(nombre: String, contrasena: String) {
        val retrofit = Retrofit.Builder()
            .baseUrl("http://10.0.2.2:8000/api/")
            .addConverterFactory(GsonConverterFactory.create())
            .build()

        val api = retrofit.create(ComidaAPI::class.java)

        api.login(nombre, contrasena).enqueue(object : Callback<Usuario> {
            override fun onResponse(call: Call<Usuario>, response: Response<Usuario>) {
                if (response.isSuccessful) {
                    val usuarioLogueado = response.body()
                    if (usuarioLogueado != null) {
                        val prefs = requireActivity().getSharedPreferences("AUTH", Context.MODE_PRIVATE)

                        prefs.edit().putInt("USER_ID", usuarioLogueado.id ?: -1).apply()
                        prefs.edit().putString("USER_NAME", usuarioLogueado.nombre).apply()
                        prefs.edit().putString("USER_ROL", usuarioLogueado.rol).apply()

                        Toast.makeText(requireContext(), "Bienvenido ${usuarioLogueado.nombre}", Toast.LENGTH_SHORT).show()
                        
                        // Usamos la acción para navegar y limpiar el stack del Login
                        findNavController().navigate(R.id.action_login_to_pedidos)
                    }
                } else if (response.code() == 401) {
                    Toast.makeText(requireContext(), "Usuario o contraseña incorrectos", Toast.LENGTH_SHORT).show()
                } else {
                    Log.e("API_ERROR", "Error: ${response.code()}")
                    Toast.makeText(requireContext(), "Error en el servidor", Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<Usuario>, t: Throwable) {
                Log.e("API_ERROR", "Fallo de red: ${t.message}")
                Toast.makeText(requireContext(), "No se pudo conectar con el servidor", Toast.LENGTH_SHORT).show()
            }
        })
    }
}
