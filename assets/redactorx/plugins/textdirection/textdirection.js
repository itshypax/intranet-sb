RedactorX.add("plugin", "textdirection", {
  translations: {
    en: {
      textdirection: {
        title: "RTL-LTR",
        ltr: "Left to Right",
        rtl: "Right to Left",
      },
    },
  },
  defaults: {
    icon: '<svg height="16" viewbox="0 0 16 16" width="16" xmlns="http://www.w3.org/2000/svg"><path d="m3.8 8.10002943c.12590292.09442719.2.24262134.2.4v1.49999997h9c.5522847 0 1 .4477153 1 1 0 .5128359-.3860402.9355072-.8833789.9932723l-.1166211.0067277h-9v1.5c0 .2761424-.22385763.5-.5.5-.15737865 0-.30557281-.0740971-.4-.2l-1.875-2.5c-.13333333-.1777778-.13333333-.4222222 0-.6l1.875-2.49999997c.16568542-.2209139.4790861-.26568543.7-.1zm7.4-6c.2209139-.16568543.5343146-.1209139.7.1l1.875 2.5c.1333333.17777777.1333333.42222222 0 .6l-1.875 2.5c-.0944272.12590292-.2426213.2-.4.2-.2761424 0-.5-.22385763-.5-.5v-1.5h-9c-.55228475 0-1-.44771525-1-1 0-.51283584.38604019-.93550716.88337887-.99327227l.11662113-.00672773 8.999-.00002943.001-1.49997057c0-.12590293.0474221-.24592777.1308533-.33724831z"/></svg>',
  },
  start: function () {
    this.app.toolbar.add("textdirection", {
      title: "## textdirection.title ##",
      icon: this.opts.textdirection.icon,
      command: "textdirection.toggle",
      blocks: {
        all: "editable",
        types: ["pre"],
      },
    });
  },
  toggle: function (params, button) {
    var instance = this.app.block.get();
    var $block = instance.getBlock();
    var currentDir = $block.attr("dir");
    var items = {
      ltr: { title: "## textdirection.ltr ##", command: "textdirection.set" },
      rtl: { title: "## textdirection.rtl ##", command: "textdirection.set" },
    };
    var dir = currentDir ? currentDir : this.opts.editor.direction;

    items[dir].active = true;

    this.app.popup.create("textdirection", { items: items });
    this.app.popup.open({ button: button });
  },
  set: function (params, item, name) {
    this.app.popup.close();

    var instance = this.app.block.get();
    var $block = instance.getBlock();
    var dir = this.opts.editor.direction;

    if (dir === name) {
      $block.removeAttr("dir");
    } else {
      $block.attr("dir", name);
    }
  },
});
