//process.env.YENV = 'production';

var
	LevelNode = require('bem/lib/nodes/level').LevelNode,
	BundlesLevelNode = require('bem/lib/nodes/level').BundlesLevelNode,

	basePaths = ['app/bemyeah']; //, 'module/bemyeah', 'core/bemyeah'],
	qwepages = function () {
		
	};

//console.log(PATH.resolve, LevelNode, BundlesLevelNode);

MAKE.decl('Arch', {

    getLibraries: function() {

        return {
            'core/bem-bl': {
                type: 'git',
                url: 'git://github.com/bem/bem-bl.git',
                treeish: '0.3'
            }
        };

    },

//	bundlesLevelsRegexp: /^(pages.*|bundles.*)/i,
//	blocksLevelsRegexp:  /^(blocks.*)/i,

	// make custom level nodes
	createBlocksLevelsNodes: function(parent, children) {
		var nodeIds = [], that = this;

		basePaths.forEach(function (v, k) {
			// Create instance of LevelNode
			var node = new LevelNode({
				level: v+'/blocks'
			});
			// Add created node to arch tree
			this.arch.setNode(node, parent, children);
			// Push node id to result list
			nodeIds.push(node.getId());
		}, this);

		// Returns list with ids of created nodes
		return nodeIds;
	},

	// and pages/bundle nodes
	createBundlesLevelsNodes: function(parent, children) {
		var nodeIds = [];

		basePaths.forEach(function (v, k) {
			// Create instance of LevelNode
			var node = new BundlesLevelNode({
				level: v+'/pages'
			});
			// Add created node to arch tree
			this.arch.setNode(node, parent, children);
			// Push node id to result list
			nodeIds.push(node.getId());
		}, this);

		// Возвращаем массив из идентификаторов созданных узлов
		return nodeIds;
	}//*/

});



MAKE.decl('BundleNode', {

    getTechs: function() {

        return [
            'bemjson.js',
            'bemdecl.js',
            'deps.js',
            'bemhtml',
            'js',
            'css',
            'ie.css',
            'ie6.css',
            'ie7.css',
            'ie8.css',
            'ie9.css',
            'html'
        ];
    }

});
