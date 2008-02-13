/*
 * (c) Edutice 2007 http://www.edutice.fr
 * Author : vbe <v.bataille@novatice.com>
 * Version : 00.00.01
 * Description : demo of editable tree javascript functions
 * Note : Edutice is a brand of Novatice Technologies SAS 
 */
var TreeId = "treeieml";
 
//ajout lsd variable global pour gérer les multi-tri
function initTree(nomTree) {
	//alert(nomTree);
	TreeId = nomTree;
	var vbTreeRing = document.getElementById(TreeId).parentNode;
}  
  
/*start the edition*/
function startEditable(event, col) {

	vbTreeRing = document.getElementById(TreeId).parentNode;

    //definition of edtion type for each column to edit
	//alert(col);
	//toute les colonnes sont éditable
	if(col.substr(0,8)=='treecol_'){
		var tbAge = vbTreeRing.updateByTextbox("([A-Za-z0-9])", "tipBadValue");
		//4 characters maximum
		tbAge.setAttribute('maxlength', 255);
		vbTreeRing.locateEditable(tbAge);
    }

    switch (col) {
		case 'per_community':
            //update by checkbox
            vbTreeRing.updateByCb(event);
            break;

        case 'per_ring':
            //update by radio
            vbTreeRing.updateByRadio();
            break;

        case 'per_race':
            //update by list
            var pop = document.createElement('menupopup');
            var it1 = document.createElement('menuitem');
            it1.setAttribute('label', 'Elfe');
            it1.setAttribute('value', 'Elfe');
            var it2 = document.createElement('menuitem');
            it2.setAttribute('label', 'Hobbit');
            it2.setAttribute('value', 'Hobbit');
            var it3 = document.createElement('menuitem');
            it3.setAttribute('label', 'Homme');
            it3.setAttribute('value', 'Homme');

            pop.appendChild(it1);
            pop.appendChild(it2);
            pop.appendChild(it3);

            var list = vbTreeRing.updateByList(pop);
            vbTreeRing.locateEditable(list);
            break;

        case 'per_actor_name':
            //update by rdf list
            var source = 'everyActors.rdf';
            var lab = 'rdf:http://actors/rdf#act_name';
            var val = 'rdf:http://actors/rdf#act_id';
            var ml = vbTreeRing.updateByRdfList(source, 'urn:data:actors', lab, val);

            vbTreeRing.locateEditable(ml);
            break;

        case 'per_actor_birth':
            //update by textbox
            //allow only 4 figures or nothing
            var tbAge = vbTreeRing.updateByTextbox("(^[0-9]{4}$|^$)", "tipBadValue");
            //4 characters maximum
            tbAge.setAttribute('maxlength', 4);

            vbTreeRing.locateEditable(tbAge);
            break;

        default:
            break;
    }
}

/*save the new value*/
function saveEditable(element,typesource) {
	tree = document.getElementById(TreeId).parentNode;
	type = tree.firstChild.getAttribute('typesource');
	var idCol = element.getAttribute('currentField');
	var idToUpdate = element.getAttribute('currentId');
    //alert('saveEditable !'+type+' '+idCol+' '+idToUpdate+' '+element.value);
	SetOnto(typesource,idCol,idToUpdate,element.value);
}

/*insert a new row*/
function startInsert(event) {
    alert('startInsert !'+event);
	vbTreeRing = document.getElementById(TreeId).parentNode;
	cell = vbTreeRing.getClickedCell(event);
	alert(cell.row+' '+cell.col);
}

/*delete the row selected*/
function startDelete(idToDelete) {
    alert('A implémenter selon les besoins !');
}

/*déplace la branche*/
function MoveBranche(TreeId, dir, event) {
    //alert('moveBranche 
	vbTreeRing = document.getElementById(TreeId).parentNode;
	vbTreeRing.Move(TreeId, dir, event);
}

function startSelect(tree, cellCoord)
{

    //alert('startSelect ! '+element);
	type = tree.firstChild.getAttribute('typesource');

	//affichage pour la traduction
	try {
		txtId = document.getElementById("id-trad-"+type);
		txtCode = document.getElementById("code-trad-"+type);
		txtLib = document.getElementById("lib-trad-"+type);

		txtId.value = tree.getCell(cellCoord.row, 'id').getAttribute('label');
		txtCode.value = tree.getCell(cellCoord.row, 'treecol_code').getAttribute('label');
		txtLib.value = tree.getCell(cellCoord.row, 'treecol_lib').getAttribute('label');
	}
	catch (e) {
	}

}

