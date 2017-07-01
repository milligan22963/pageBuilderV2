/**
 * Inheritance
 * 
 * see: http://phrogz.net/js/classes/OOPinJS2.html
 */

Function.prototype.inheritsFrom = function(parentObj)
{
    // normal object i.e. function
    if (parentObj.constructor == Function)
    {
        this.prototype = new parentObj;
        this.prototype.parent.push(parentObj.prototype);
    }
    else // "pure virtual"
    {
        this.prototype = parentObj;
        this.prototype.parent.push(parentObj);
    }
    this.prototype.constructor = this;
    this.prototype[this.prototype.constructor.name] = this.prototype.parent.length - 1;

    return this;
}