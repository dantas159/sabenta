# Sabenta — Landing Page

Site institucional da Sabenta (desenvolvimento de sites, sistemas e automações — Itabuna, Bahia). HTML/CSS/JS estático, sem build step e sem dependências de runtime.

## Estrutura

```
index.html            → página de produção (é o que vai para o ar)
assets/
  sabenta-logo.png     → logo usada no header e no rodapé
  css/
    styles.css          → tokens e componentes do design system (cores, botões, cards, tags...)
    site.css             → overrides de tema da página, animações e estados de hover
  js/
    site.js               → menu mobile, scroll reveal e comportamento do header/CTA flutuante
robots.txt / sitemap.xml → SEO básico para indexação
design/                → fonte editável original (ver abaixo) — não é usada em produção
```

### Sobre a pasta `design/`

O site foi originalmente criado no Claude Design (Design Canvas) e exportado como `design/Sabenta - Landing Page.dc.html`. Esse formato depende de um runtime (`design/support.js`) que baixa React, ReactDOM e Babel via CDN e monta a página inteira no navegador — ótimo para editar visualmente, mas ruim para produção (SEO fraco, pois o conteúdo só aparece depois do JS carregar, e carregamento mais lento).

Por isso `index.html` é uma conversão manual para HTML/CSS/JS puro: mesmo conteúdo e visual, sem dependência de CDN nem de um runtime de template. `design/` fica só como referência caso você queira reabrir o projeto no Claude Design para editar visualmente de novo; não precisa subir essa pasta para o host, mas não custa mantê-la no repositório.

`design/uploads/` e `design/.thumbnail` também são artefatos do próprio editor (histórico de upload de logo e a miniatura do projeto) — não são usados pelo site.

## Rodando localmente

Como é só HTML/CSS/JS estático, dá para abrir `index.html` direto no navegador. Para ficar mais fiel ao ambiente de produção (e evitar qualquer restrição do navegador com `file://`), rode um servidor estático simples na raiz do projeto:

```
npx serve .
# ou
python -m http.server 8000
```

## Deploy no GitHub Pages

1. Suba o conteúdo desta pasta para o branch `main` do repositório no GitHub.
2. No GitHub: **Settings → Pages → Build and deployment → Source: Deploy from a branch**.
3. Branch: `main`, pasta: `/ (root)`. Salve.
4. O site fica disponível em `https://dantas159.github.io/sabenta/` (pode levar alguns minutos na primeira vez).

Se depois quiser usar um domínio próprio (ex: `sabenta.com.br`), adicione um arquivo `CNAME` na raiz com o domínio e configure o DNS conforme a [documentação do GitHub Pages](https://docs.github.com/pages/configuring-a-custom-domain-for-your-github-pages-site). Depois disso, atualize as URLs em `index.html` (`canonical`, `og:url`), `robots.txt` e `sitemap.xml` para o novo domínio.

## Editando conteúdo

- **Número de WhatsApp / e-mail de contato**: estão fixos (hardcoded) em vários pontos do `index.html` como links `https://wa.me/...` e `mailto:...`. Se mudar, é preciso atualizar todas as ocorrências (use busca e substituição pelo número/e-mail atual).
- **Textos e seções**: é só editar diretamente o HTML em `index.html` — cada seção (`#problema`, `#solucoes`, `#metodo`, `#projetos`, `#sobre`, `#contato`) é independente.
- **Cores e tipografia**: os tokens de tema (cores, fontes, espaçamento) ficam em `:root` no topo de `assets/css/site.css`.
