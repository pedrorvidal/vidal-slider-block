/**
 * Lógica pura de manipulação dos slides — sem nada de React ou UI — pra
 * poder ser testada isoladamente com Jest, sem precisar renderizar o
 * bloco inteiro (o que exigiria simular o media picker do WordPress).
 */

// Link relativo (uma única "/", não "//..." — isso é protocol-relative,
// um vetor de open-redirect) ou absoluto (http/https). A mesma regra é
// reaplicada em render.php, que é quem de fato decide o que é renderizado —
// esta validação aqui é só para a experiência de edição.
const LINK_URL_PATTERN = /^(\/(?!\/)|https?:\/\/)/i;

export function isValidLinkUrl( url ) {
	return LINK_URL_PATTERN.test( url );
}

/**
 * Reconstrói a lista de imagens a partir de uma nova seleção da biblioteca
 * de mídia, preservando o link já configurado em cada imagem que já
 * existia. Sem isso, reabrir "Editar imagens" apagaria todos os links
 * salvos, porque a seleção do media picker não sabe nada sobre eles.
 *
 * @param {Array} previousImages Lista de imagens antes da nova seleção.
 * @param {Array} selectedImages Itens selecionados agora no media picker.
 * @return {Array} Nova lista de imagens, com o link de cada uma preservado.
 */
export function mergeSelectedImages( previousImages, selectedImages ) {
	const previousById = new Map(
		previousImages.map( ( image ) => [ image.id, image ] )
	);

	return selectedImages.map( ( image ) => {
		const previous = previousById.get( image.id );
		return {
			id: image.id,
			url: image.url,
			alt: image.alt || '',
			link: previous?.link,
		};
	} );
}

/**
 * Verdadeiro se alguma imagem tiver um link preenchido, mas inválido.
 *
 * @param {Array} images Lista de imagens do bloco.
 * @return {boolean} Se existe algum link inválido entre as imagens.
 */
export function hasAnyInvalidLink( images ) {
	return images.some( ( image ) => {
		const url = ( image.link?.url ?? '' ).trim();
		return url !== '' && ! isValidLinkUrl( url );
	} );
}
