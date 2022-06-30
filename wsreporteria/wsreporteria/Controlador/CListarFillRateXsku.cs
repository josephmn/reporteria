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
    public class CListarFillRateXsku
    {
        public List<EListarFillRateXsku> ListarFillRateXsku(SqlConnection con, Int32 post, String almacen)
        {
            List<EListarFillRateXsku> lEListarFillRateXsku = null;
            SqlCommand cmd = new SqlCommand("ASP_LISTAR_FILL_RATE_SKU", con);
            cmd.CommandType = CommandType.StoredProcedure;

            cmd.Parameters.AddWithValue("@post", SqlDbType.Int).Value = post;
            cmd.Parameters.AddWithValue("@almacen", SqlDbType.VarChar).Value = almacen;

            SqlDataReader drd = cmd.ExecuteReader(CommandBehavior.SingleResult);

            if (drd != null)
            {
                lEListarFillRateXsku = new List<EListarFillRateXsku>();

                EListarFillRateXsku obEListarFillRateXsku = null;
                while (drd.Read())
                {
                    obEListarFillRateXsku = new EListarFillRateXsku();
                    obEListarFillRateXsku.ALMACEN = drd["ALMACEN"].ToString();
                    obEListarFillRateXsku.PERIODO = drd["PERIODO"].ToString();
                    obEListarFillRateXsku.SKU = drd["SKU"].ToString();
                    obEListarFillRateXsku.NOMBRE_PRODUCTO = drd["NOMBRE_PRODUCTO"].ToString();
                    obEListarFillRateXsku.FAMILIA = drd["FAMILIA"].ToString();
                    obEListarFillRateXsku.CANTIDAD_ORDEN = Convert.ToInt32(drd["CANTIDAD_ORDEN"].ToString());
                    obEListarFillRateXsku.CANTIDAD_LLEVA = Convert.ToInt32(drd["CANTIDAD_LLEVA"].ToString());
                    obEListarFillRateXsku.PORCENTAJE = Convert.ToDouble(drd["PORCENTAJE"].ToString());
                    obEListarFillRateXsku.ROW = Convert.ToInt32(drd["ROW"].ToString());
                    obEListarFillRateXsku.GRUPO = drd["GRUPO"].ToString();
                    obEListarFillRateXsku.COMENTARIO = drd["COMENTARIO"].ToString();
                    lEListarFillRateXsku.Add(obEListarFillRateXsku);
                }
                drd.Close();
            }

            return (lEListarFillRateXsku);
        }
    }
}