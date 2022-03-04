/**
 * Добавление обработчика выполняемого после загрузки документа.
 * 
 * @param callable handler обработчик 
 */
function kontur_onload(handler) 
{
	return document.addEventListener("DOMContentLoaded", handler);
}