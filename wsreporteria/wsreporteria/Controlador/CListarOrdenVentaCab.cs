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
    public class CListarOrdenVentaCab
    {
        public List<EListarOrdenVentaCab> ListarOrdenVentaCab(SqlConnection con, Int32 post)
        {
            List<EListarOrdenVentaCab> lEListarOrdenVentaCab = null;
            SqlCommand cmd = new SqlCommand("ASP_LISTAR_ORDEN_VENTA_CAB", con);
            cmd.CommandType = CommandType.StoredProcedure;

            cmd.Parameters.AddWithValue("@post", SqlDbType.Int).Value = post;

            SqlDataReader drd = cmd.ExecuteReader(CommandBehavior.SingleResult);

            if (drd != null)
            {
                lEListarOrdenVentaCab = new List<EListarOrdenVentaCab>();

                EListarOrdenVentaCab obEListarOrdenVentaCab = null;
                while (drd.Read())
                {
                    obEListarOrdenVentaCab = new EListarOrdenVentaCab();
                    obEListarOrdenVentaCab.ORDEN_VENTA = drd["ORDEN_VENTA"].ToString();
                    obEListarOrdenVentaCab.FECHA_ORDEN = drd["FECHA_ORDEN"].ToString();
                    obEListarOrdenVentaCab.PERIODO = drd["PERIODO"].ToString();
                    obEListarOrdenVentaCab.ALMACEN = drd["ALMACEN"].ToString();
                    obEListarOrdenVentaCab.RUC = drd["RUC"].ToString();
                    obEListarOrdenVentaCab.RAZON = drd["RAZON"].ToString();
                    obEListarOrdenVentaCab.MONTO_ORDEN = Convert.ToDouble(drd["MONTO_ORDEN"].ToString());
                    obEListarOrdenVentaCab.PORCEN_PENDIENTE = Convert.ToDouble(drd["PORCEN_PENDIENTE"].ToString());
                    obEListarOrdenVentaCab.PORCEN_ATENCION = Convert.ToDouble(drd["PORCEN_ATENCION"].ToString());
                    lEListarOrdenVentaCab.Add(obEListarOrdenVentaCab);
                }
                drd.Close();
            }

            return (lEListarOrdenVentaCab);
        }
    }
}