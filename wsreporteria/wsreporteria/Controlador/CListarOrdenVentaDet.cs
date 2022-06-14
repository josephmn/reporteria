using System;
using System.Collections.Generic;
using System.Collections.ObjectModel;
using System.Collections.Specialized;
using System.Linq;
using System.Web;
using System.Data;
using System.Data.SqlClient;
using wsreporteria.Entity;

namespace wsreporteria.Controller
{
    public class CListarOrdenVentaDet
    {
        public List<EListarOrdenVentaDet> ListarOrdenVentaDet(SqlConnection con, String orden)
        {
            List<EListarOrdenVentaDet> lEListarOrdenVentaDet = null;
            SqlCommand cmd = new SqlCommand("ASP_LISTAR_ORDER_VENTA_DET", con);
            cmd.CommandType = CommandType.StoredProcedure;

            cmd.Parameters.AddWithValue("@orden", SqlDbType.VarChar).Value = orden;

            SqlDataReader drd = cmd.ExecuteReader(CommandBehavior.SingleResult);

            if (drd != null)
            {
                lEListarOrdenVentaDet = new List<EListarOrdenVentaDet>();

                EListarOrdenVentaDet obEListarOrdenVentaDet = null;
                while (drd.Read())
                {
                    obEListarOrdenVentaDet = new EListarOrdenVentaDet();
                    obEListarOrdenVentaDet.ORDEN_VENTA = drd["ORDEN_VENTA"].ToString();
                    obEListarOrdenVentaDet.FECHA_ORDEN = Convert.ToDateTime(drd["FECHA_ORDEN"].ToString());
                    obEListarOrdenVentaDet.ALMACEN = drd["ALMACEN"].ToString();
                    obEListarOrdenVentaDet.RUC = drd["RUC"].ToString();
                    obEListarOrdenVentaDet.RAZON = drd["RAZON"].ToString();
                    obEListarOrdenVentaDet.FILA = Convert.ToInt32(drd["FILA"].ToString());
                    obEListarOrdenVentaDet.SKU = drd["SKU"].ToString();
                    obEListarOrdenVentaDet.NOMBRE_PRODUCTO = drd["NOMBRE_PRODUCTO"].ToString();
                    obEListarOrdenVentaDet.PRECIO = Convert.ToDouble(drd["PRECIO"].ToString());
                    obEListarOrdenVentaDet.CANTIDAD_ORDEN = Convert.ToDouble(drd["CANTIDAD_ORDEN"].ToString());
                    obEListarOrdenVentaDet.MONTO_TOTAL = Convert.ToDouble(drd["MONTO_TOTAL"].ToString());
                    obEListarOrdenVentaDet.MONTO_RESTANTE = Convert.ToDouble(drd["MONTO_RESTANTE"].ToString());
                    lEListarOrdenVentaDet.Add(obEListarOrdenVentaDet);
                }
                drd.Close();
            }

            return (lEListarOrdenVentaDet);
        }
    }
}