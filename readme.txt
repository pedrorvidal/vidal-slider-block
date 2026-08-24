=== Vidal Slider Block ===
Contributors:      pedrorvidal
Tags:               block, slider, carousel, gallery, gutenberg
Requires at least:  6.8
Tested up to:       6.8
Requires PHP:       7.4
Stable tag:         0.1.0
License:            GPL-2.0-or-later
License URI:        https://www.gnu.org/licenses/gpl-2.0.html

Bloco Gutenberg para criar um slider de imagens, com opção de largura total, autoplay configurável e navegação por bolinhas.

== Description ==

Vidal Slider Block adiciona um bloco nativo ao editor do WordPress (Gutenberg) para montar sliders de imagens sem precisar de shortcodes ou plugins pesados de terceiros.

**⚠️ Este plugin ainda está em construção.** As funcionalidades abaixo já funcionam no editor, mas a navegação automática e por bolinhas no front-end ainda está sendo implementada.

Funcionalidades:

* Seleção de múltiplas imagens direto da Biblioteca de Mídia do WordPress.
* Opção de layout: conteúdo (boxed) ou largura total (full width).
* Autoplay configurável (ligar/desligar).
* Intervalo entre slides ajustável (de 1 a 10 segundos).
* Navegação por bolinhas (dots) quando há mais de uma imagem.
* Bloco dinâmico: a marcação é gerada no servidor a cada carregamento da página.

== Installation ==

= Via upload manual =

1. Faça o download do plugin (ou clone este repositório).
2. Envie a pasta `vidal-slider-block` para o diretório `/wp-content/plugins/`.
3. Ative o plugin em **Plugins** no painel do WordPress.
4. No editor de blocos, adicione o bloco **Vidal Slider Block** (categoria "Widgets").

= A partir do código-fonte (desenvolvimento) =

Se você clonou o repositório diretamente, é necessário compilar os assets antes de ativar o plugin:

`
npm install
npm run build
`

Isso gera a pasta `build/`, que é a que o plugin de fato registra e carrega no WordPress.

== Usage ==

1. No editor, clique em **Adicionar bloco** e procure por "Vidal Slider Block".
2. Clique em **Adicionar imagens** e selecione (ou envie) uma ou mais imagens na Biblioteca de Mídia.
3. No painel lateral (**Configurações do Slider**), ajuste:
   * **Full width** – faz o slider ocupar toda a largura da tela.
   * **Autoplay** – liga/desliga o avanço automático dos slides.
   * **Intervalo entre slides** – define, em segundos, o tempo entre uma troca e outra (quando o autoplay está ativo).
4. Para trocar as imagens depois, use o botão **Editar imagens**; para remover uma imagem específica, passe o mouse sobre ela e clique em **Remover**.
5. Publique ou atualize a página/post para ver o slider no front-end.

== Frequently Asked Questions ==

= O slider funciona sem JavaScript no front-end? =

A marcação (slides e bolinhas de navegação) é sempre renderizada no servidor. O comportamento interativo (autoplay e clique nas bolinhas) depende do script de front-end, que ainda está em desenvolvimento nesta versão.

= Posso usar vídeos ou outros tipos de mídia? =

Não. Por enquanto o bloco aceita apenas imagens.

= Preciso rodar `npm run build` sempre que atualizar o plugin? =

Só se você estiver trabalhando a partir do código-fonte (pasta `src/`). Se você baixou uma versão já empacotada/zipada do plugin, a pasta `build/` já vem pronta.

== Changelog ==

= 0.1.0 =
* Versão inicial: seleção de imagens, configurações de layout/autoplay/intervalo e renderização dinâmica no front-end.
