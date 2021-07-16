/*Fun��o Pai de Mascaras*/
function Mascara(o, f) {
	v_obj = o
	v_fun = f
	setTimeout("execmascara()", 1)
}

/*Fun��o que Executa os objetos*/
function execmascara() {
	v_obj.value = v_fun(v_obj.value)
}

/*Fun��o que Determina as express�es regulares dos objetos*/
function leech(v) {
	v = v.replace(/o/gi, "0")
	v = v.replace(/i/gi, "1")
	v = v.replace(/z/gi, "2")
	v = v.replace(/e/gi, "3")
	v = v.replace(/a/gi, "4")
	v = v.replace(/s/gi, "5")
	v = v.replace(/t/gi, "7")
	return v
}

/*Fun��o que permite apenas numeros*/
function Integer(v) {
	return v.replace(/\D/g, "")
}

/*Fun��o que padroniza telefone (11) 4184.1241*/
function Telefone(v) {
	v = v.replace(/\D/g, "")
	v = v.replace(/^(\d\d)(\d)/g, "($1) $2")
	v = v.replace(/(\d{4})(\d)/, "$1.$2")
	return v
}

/*Fun��o que padroniza telefone (11) 41841241*/
function TelefoneCall(v) {
	v = v.replace(/\D/g, "")
	v = v.replace(/^(\d\d)(\d)/g, "($1) $2")
	return v
}

/*Fun��o que padroniza CPF*/
function Cpf(v) {
	v = v.replace(/\D/g, "")
	v = v.replace(/(\d{3})(\d)/, "$1.$2")
	v = v.replace(/(\d{3})(\d)/, "$1.$2")

	v = v.replace(/(\d{3})(\d{1,2})$/, "$1-$2")
	return v
}

/*Fun��o que padroniza CEP*/
function Cep(v) {
	v = v.replace(/\D/g, "")
	v = v.replace(/^(\d{5})(\d)/, "$1-$2")
	return v
}

/*Fun��o que padroniza CNPJ*/
function Cnpj(v) {
	v = v.replace(/\D/g, "")
	v = v.replace(/^(\d{2})(\d)/, "$1.$2")
	v = v.replace(/^(\d{2})\.(\d{3})(\d)/, "$1.$2.$3")
	v = v.replace(/\.(\d{3})(\d)/, ".$1/$2")
	v = v.replace(/(\d{4})(\d)/, "$1-$2")
	return v
}

/*Fun��o que permite apenas numeros Romanos*/
function Romanos(v) {
	v = v.toUpperCase()
	v = v.replace(/[^IVXLCDM]/g, "")

	while (v.replace(/^M{0,4}(CM|CD|D?C{0,3})(XC|XL|L?X{0,3})(IX|IV|V?I{0,3})$/, "") != "")
	v = v.replace(/.$/, "")
	return v
}

/*Fun��o que padroniza o Site*/
function Site(v) {
	v = v.replace(/^http:\/\/?/, "")
	dominio = v
	caminho = ""
	if (v.indexOf("/") > -1)
		dominio = v.split("/")[0]
	caminho = v.replace(/[^\/]*/, "")
	dominio = dominio.replace(/[^\w\.\+-:@]/g, "")
	caminho = caminho.replace(/[^\w\d\+-@:\?&=%\(\)\.]/g, "")
	caminho = caminho.replace(/([\?&])=/, "$1")
	if (caminho != "")
		dominio = dominio.replace(/\.+$/, "")
	v = "http://" + dominio + caminho
	return v
}

/*Fun��o que padroniza DATA*/
function Data(v) {
	v = v.replace(/\D/g, "")
	v = v.replace(/(\d{2})(\d)/, "$1/$2")
	v = v.replace(/(\d{2})(\d)/, "$1/$2")
	return v
}

/*Fun��o que padroniza DATA*/
function Hora(v) {
	v = v.replace(/\D/g, "")
	v = v.replace(/(\d{2})(\d)/, "$1:$2")
	return v
}

/*Fun��o que padroniza valor mon�tario*/
function Valor(v) {
	v = v.replace(/\D/g, "")//Remove tudo o que n�o � d�gito
	v = v.replace(/^([0-9]{3}\.?){3}-[0-9]{2}$/, "$1.$2");
	//v=v.replace(/(\d{3})(\d)/g,"$1,$2")
	v = v.replace(/(\d)(\d{2})$/, "$1,$2")//Coloca ponto antes dos 2 �ltimos digitos
	return v
}

/*Fun��o que padroniza Area*/
function Area(v) {
	v = v.replace(/\D/g, "")
	v = v.replace(/(\d)(\d{2})$/, "$1,$2")
	return v

}

/*Fun��o que padroniza placa de veiculo carro AAA-9999*/
function Placa(v) {
	v = v.toUpperCase()
	return v
}

// construindo o calend�rio
function popdate(obj, div, tam, ddd) {
	if (ddd) {
		day = ""
		mmonth = ""
		ano = ""
		c = 1
		char = ""
		for ( s = 0; s < parseInt(ddd.length); s++) {
			char = ddd.substr(s, 1)
			if (char == "/") {
				c++;
				s++;
				char = ddd.substr(s, 1);
			}
			if (c == 1)
				day += char
			if (c == 2)
				mmonth += char
			if (c == 3)
				ano += char
		}

		ddd = mmonth + "/" + day + "/" + ano
	}

	if (!ddd) {
		today = new Date()
	} else {
		today = new Date(ddd)
	}
	date_Form = eval(obj)
	if (date_Form.value == "") {
		date_Form = new Date()
	} else {
		date_Form = new Date(date_Form.value)
	}

	ano = today.getFullYear();
	mmonth = today.getMonth();
	day = today.toString().substr(8, 2)

	umonth = new Array("Janeiro", "Fevereiro", "Mar&ccedil;o", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro")
	days_Feb = (!(ano % 4) ? 29 : 28)
	days = new Array(31, days_Feb, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31)

	if ((mmonth < 0) || (mmonth > 11))
		alert(mmonth)
	if ((mmonth - 1) == -1) {
		month_prior = 11;
		year_prior = ano - 1
	} else {
		month_prior = mmonth - 1;
		year_prior = ano
	}
	if ((mmonth + 1) == 12) {
		month_next = 0;
		year_next = ano + 1
	} else {
		month_next = mmonth + 1;
		year_next = ano
	}
	txt = "<table bgcolor='#efefff' style='border:solid #330099; border-width:2' cellspacing='0' cellpadding='3' border='0' width='" + tam + "' height='" + tam * 1.1 + "'>"
	txt += "<tr bgcolor='#FFFFFF'><td colspan='7' align='center'><table border='0' cellpadding='0' width='100%' bgcolor='#FFFFFF'><tr>"
	txt += "<td width=20% align=center><a href=javascript:popdate('" + obj + "','" + div + "','" + tam + "','" + ((mmonth + 1).toString() + "/01/" + (ano - 1).toString()) + "') class='Cabecalho_Calendario' title='Ano Anterior'><<</a></td>"
	txt += "<td width=20% align=center><a href=javascript:popdate('" + obj + "','" + div + "','" + tam + "','" + ("01/" + (month_prior + 1).toString() + "/" + year_prior.toString()) + "') class='Cabecalho_Calendario' title='M&ecirc;s Anterior'><</a></td>"
	txt += "<td width=20% align=center><a href=javascript:popdate('" + obj + "','" + div + "','" + tam + "','" + ("01/" + (month_next + 1).toString() + "/" + year_next.toString()) + "') class='Cabecalho_Calendario' title='Pr&oacute;ximo M&ecirc;s'>></a></td>"
	txt += "<td width=20% align=center><a href=javascript:popdate('" + obj + "','" + div + "','" + tam + "','" + ((mmonth + 1).toString() + "/01/" + (ano + 1).toString()) + "') class='Cabecalho_Calendario' title='Pr&oacute;ximo Ano'>>></a></td>"
	txt += "<td width=20% align=right><a href=javascript:force_close('" + div + "') class='Cabecalho_Calendario' title='Fechar Calend&aacute;rio'><b>X</b></a></td></tr></table></td></tr>"
	txt += "<tr><td colspan='7' align='right' bgcolor='#ccccff' class='mes'><a href=javascript:pop_year('" + obj + "','" + div + "','" + tam + "','" + (mmonth + 1) + "') class='mes'>" + ano.toString() + "</a>"
	txt += " <a href=javascript:pop_month('" + obj + "','" + div + "','" + tam + "','" + ano + "') class='mes'>" + umonth[mmonth] + "</a> <div id='popd' style='position:absolute'></div></td></tr>"
	txt += "<tr bgcolor='#330099'><td width='14%' class='dia' align=center><b>Dom</b></td><td width='14%' class='dia' align=center><b>Seg</b></td><td width='14%' class='dia' align=center><b>Ter</b></td><td width='14%' class='dia' align=center><b>Qua</b></td><td width='14%' class='dia' align=center><b>Qui</b></td><td width='14%' class='dia' align=center><b>Sex<b></td><td width='14%' class='dia' align=center><b>Sab</b></td></tr>"
	today1 = new Date((mmonth + 1).toString() + "/01/" + ano.toString());
	diainicio = today1.getDay() + 1;
	week = d = 1
	start = false;

	for ( n = 1; n <= 42; n++) {
		if (week == 1)
			txt += "<tr bgcolor='#efefff' align=center>"
		if (week == diainicio) {
			start = true
		}
		if (d > days[mmonth]) {
			start = false
		}
		if (start) {
			dat = new Date((mmonth + 1).toString() + "/" + d + "/" + ano.toString())
			day_dat = dat.toString().substr(0, 10)
			day_today = date_Form.toString().substr(0, 10)
			year_dat = dat.getFullYear()
			year_today = date_Form.getFullYear()
			colorcell = ((day_dat == day_today) && (year_dat == year_today) ? " bgcolor='#FFCC00' " : "" )
			var variavelMes = mmonth + 1;
			if (variavelMes <= 9 && d > 9) {
				txt += "<td" + colorcell + " align=center><a href=javascript:block('" + d + "/0" + (mmonth + 1).toString() + "/" + ano.toString() + "','" + obj + "','" + div + "') class='data'>" + d.toString() + "</a></td>"
			} else if (d <= 9 && variavelMes > 9) {
				txt += "<td" + colorcell + " align=center><a href=javascript:block('0" + d + "/" + (mmonth + 1).toString() + "/" + ano.toString() + "','" + obj + "','" + div + "') class='data'>" + d.toString() + "</a></td>"
			} else if (variavelMes <= 9 && d <= 9) {
				txt += "<td" + colorcell + " align=center><a href=javascript:block('0" + d + "/0" + (mmonth + 1).toString() + "/" + ano.toString() + "','" + obj + "','" + div + "') class='data'>" + d.toString() + "</a></td>"
			} else {
				txt += "<td" + colorcell + " align=center><a href=javascript:block('" + d + "/" + (mmonth + 1).toString() + "/" + ano.toString() + "','" + obj + "','" + div + "') class='data'>" + d.toString() + "</a></td>"
			}

			d++
		} else {
			txt += "<td class='data' align=center> </td>"
		}
		week++
		if (week == 8) {
			week = 1;
			txt += "</tr>"
		}
	}
	txt += "</table>"
	div2 = eval(div)
	div2.innerHTML = txt
}

// fun��o para exibir a janela com os meses
function pop_month(obj, div, tam, ano) {
	txt = "<table bgcolor='#CCCCFF' border='0' width=80>"
	for ( n = 0; n < 12; n++) {
		txt += "<tr><td align=center><a href=javascript:popdate('" + obj + "','" + div + "','" + tam + "','" + ("01/" + (n + 1).toString() + "/" + ano.toString()) + "')>" + umonth[n] + "</a></td></tr>"
	}
	txt += "</table>"
	popd.innerHTML = txt
}

// fun��o para exibir a janela com os anos
function pop_year(obj, div, tam, umonth) {
	txt = "<table bgcolor='#CCCCFF' border='0' width=160>"
	l = 1
	for ( n = 2012; n < 2033; n++) {
		if (l == 1)
			txt += "<tr>"
		txt += "<td align=center><a href=javascript:popdate('" + obj + "','" + div + "','" + tam + "','" + (umonth.toString() + "/01/" + n) + "')>" + n + "</a></td>"
		l++
		if (l == 4) {
			txt += "</tr>";
			l = 1
		}
	}
	txt += "</tr></table>"
	popd.innerHTML = txt
}

// fun��o para fechar o calend�rio
function force_close(div) {
	div2 = eval(div);
	div2.innerHTML = ''
}

// fun��o para fechar o calend�rio e setar a data no campo de data associado
function block(data, obj, div) {
	force_close(div)
	obj2 = eval(obj)
	obj2.value = data
}

